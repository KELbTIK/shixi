 <?php

class cfb {
    protected $data = "";
    protected $sectorShift = 9;
    protected $miniSectorShift = 6;
    protected $miniSectorCutoff = 4096;

    protected $fatChains = array();
    protected $fatEntries = array();

    protected $miniFATChains = array();
    protected $miniFAT = "";

    private $version = 3;
    private $isLittleEndian = true;

    private $cDir = 0;
    private $fDir = 0;

    private $cFAT = 0;

    private $cMiniFAT = 0;
    private $fMiniFAT = 0;

    private $DIFAT = array();
    private $cDIFAT = 0;
    private $fDIFAT = 0;

    const ENDOFCHAIN = 0xFFFFFFFE;
    const FREESECT   = 0xFFFFFFFF;

    public function read($filename) {
        $this->data = file_get_contents($filename);
    }

    public function parse() {
        $abSig = strtoupper(bin2hex(substr($this->data, 0, 8)));
        if ($abSig != "D0CF11E0A1B11AE1" && $abSig != "0E11FC0DD0CF11E0") { return false; }

        $this->readHeader();
        $this->readDIFAT();
        $this->readFATChains();
        $this->readMiniFATChains();
        $this->readDirectoryStructure();

        $reStreamID = $this->getStreamIdByName("Root Entry");
        if ($reStreamID === false) { return false; }
        $this->miniFAT = $this->getStreamById($reStreamID, true);

        unset($this->DIFAT);
    }

    public function getStreamIdByName($name, $from = 0) {
        for($i = $from; $i < count($this->fatEntries); $i++) {
            if ($this->fatEntries[$i]["name"] == $name)
                return $i;
        }
        return false;
    }
    public function getStreamById($id, $isRoot = false) {
        $entry = $this->fatEntries[$id];
        $from = $entry["start"];
        $size = $entry["size"];

        $stream = "";
        if ($size < $this->miniSectorCutoff && !$isRoot) {
            $ssize = 1 << $this->miniSectorShift;
            do {
                $start = $from << $this->miniSectorShift;
                $stream .= substr($this->miniFAT, $start, $ssize);
                $from = isset($this->miniFATChains[$from]) ? $this->miniFATChains[$from] : self::ENDOFCHAIN;
            } while ($from != self::ENDOFCHAIN);
        } else {
            $ssize = 1 << $this->sectorShift;
            do {
                $start = ($from + 1) << $this->sectorShift;
                $stream .= substr($this->data, $start, $ssize);
                #if (!isset($this->fatChains[$from]))
                #    $from = self::ENDOFCHAIN;
                #elseif ($from != self::ENDOFCHAIN && $from != self::FREESECT)
                #    $from = $this->fatChains[$from];
                $from = isset($this->fatChains[$from]) ? $this->fatChains[$from] : self::ENDOFCHAIN;
            } while ($from != self::ENDOFCHAIN);
        }
        return substr($stream, 0, $size);
    }

    private function readHeader() {
        $uByteOrder = strtoupper(bin2hex(substr($this->data, 0x1C, 2)));
        $this->isLittleEndian = $uByteOrder == "FEFF";
        $this->version = $this->getShort(0x1A);

        $this->sectorShift = $this->getShort(0x1E);
        $this->miniSectorShift = $this->getShort(0x20);
        $this->miniSectorCutoff = $this->getLong(0x38);

        if ($this->version == 4)
            $this->cDir = $this->getLong(0x28);
        $this->fDir = $this->getLong(0x30);

        $this->cFAT = $this->getLong(0x2C);

        $this->cMiniFAT = $this->getLong(0x40);
        $this->fMiniFAT = $this->getLong(0x3C);

        $this->cDIFAT = $this->getLong(0x48);
        $this->fDIFAT = $this->getLong(0x44);
    }

    private function readDIFAT() {
        $this->DIFAT = array();
        for ($i = 0; $i < 109; $i++)
            $this->DIFAT[$i] = $this->getLong(0x4C + $i * 4);

        if ($this->fDIFAT != self::ENDOFCHAIN) {
            $size = 1 << $this->sectorShift;
            $from = $this->fDIFAT;
            $j = 0;

            do {
                $start = ($from + 1) << $this->sectorShift;
                for ($i = 0; $i < ($size - 4); $i += 4)
                    $this->DIFAT[] = $this->getLong($start + $i);
                $from = $this->getLong($start + $i);
            } while ($from != self::ENDOFCHAIN && ++$j < $this->cDIFAT);
        }

        while($this->DIFAT[count($this->DIFAT) - 1] == self::FREESECT)
            array_pop($this->DIFAT);
    }
    private function readFATChains() {
        $size = 1 << $this->sectorShift;
        $this->fatChains = array();

        for ($i = 0; $i < count($this->DIFAT); $i++) {
            $from = ($this->DIFAT[$i] + 1) << $this->sectorShift;
            for ($j = 0; $j < $size; $j += 4)
                $this->fatChains[] = $this->getLong($from + $j);
        }
    }
    private function readMiniFATChains() {
        $size = 1 << $this->sectorShift;
        $this->miniFATChains = array();

        $from = $this->fMiniFAT;
        while ($from != self::ENDOFCHAIN) {
            $start = ($from + 1) << $this->sectorShift;
            for ($i = 0; $i < $size; $i += 4)
                $this->miniFATChains[] = $this->getLong($start + $i);
            $from = isset($this->fatChains[$from]) ? $this->fatChains[$from] : self::ENDOFCHAIN;
        }
    }

    private function readDirectoryStructure() {
        $from = $this->fDir;
        $size = 1 << $this->sectorShift;
        $this->fatEntries = array();
        do {
            $start = ($from + 1) << $this->sectorShift;
            for ($i = 0; $i < $size; $i += 128) {
                $entry = substr($this->data, $start + $i, 128);
                $this->fatEntries[] = array(
                    "name" => $this->utf16_to_ansi(substr($entry, 0, $this->getShort(0x40, $entry))),
                    "type" => ord($entry[0x42]),
                    "color" => ord($entry[0x43]),
                    "left" => $this->getLong(0x44, $entry),
                    "right" => $this->getLong(0x48, $entry),
                    "child" => $this->getLong(0x4C, $entry),
                    "start" => $this->getLong(0x74, $entry),
                    "size" => $this->getSomeBytes($entry, 0x78, 8),
                );
            }

            $from = isset($this->fatChains[$from]) ? $this->fatChains[$from] : self::ENDOFCHAIN;
        } while ($from != self::ENDOFCHAIN);

        while($this->fatEntries[count($this->fatEntries) - 1]["type"] == 0)
            array_pop($this->fatEntries);
    }

    private function utf16_to_ansi($in) {
        $out = "";
        for ($i = 0; $i < strlen($in); $i += 2)
            $out .= chr($this->getShort($i, $in));
        return trim($out);
    }

    protected function unicode_to_utf8($in, $check = false) {
        $out = "";
        if ($check && strpos($in, chr(0)) !== 1) {
            while (($i = strpos($in, chr(0x13))) !== false) {
                $j = strpos($in, chr(0x15), $i + 1);
                if ($j === false)
                    break;

                $in = substr_replace($in, "", $i, $j - $i);
            }
            for ($i = 0; $i < strlen($in); $i++) {
                if (ord($in[$i]) >= 32) {}
                elseif ($in[$i] == ' ' || $in[$i] == '\n') {}
                else
                    $in = substr_replace($in, "", $i, 1);
            }
            $in = str_replace(chr(0), "", $in);

            return $in;
        } elseif ($check) {
            while (($i = strpos($in, chr(0x13).chr(0))) !== false) {
                $j = strpos($in, chr(0x15).chr(0), $i + 1);
                if ($j === false)
                    break;

                $in = substr_replace($in, "", $i, $j - $i);
            }
            $in = str_replace(chr(0).chr(0), "", $in);
        }

        $skip = false;
        for ($i = 0; $i < strlen($in); $i += 2) {
            $cd = substr($in, $i, 2);
            if ($skip) {
                if (ord($cd[1]) == 0x15 || ord($cd[0]) == 0x15)
                    $skip = false;
                continue;
            }

            if (ord($cd[1]) == 0) {
                if (ord($cd[0]) >= 32)
                    $out .= $cd[0];
                elseif ($cd[0] == ' ' || $cd[0] == '\n')
                    $out .= $cd[0];
                elseif (ord($cd[0]) == 0x13)
                    $skip = true;
                else {
                    continue;
                    switch (ord($cd[0])) {
                        case 0x0D: case 0x07: $out .= "\n"; break;
                        case 0x08: case 0x01: $out .= ""; break;
                        case 0x13: $out .= "HYPER13"; break;
                        case 0x14: $out .= "HYPER14"; break;
                        case 0x15: $out .= "HYPER15"; break;
                        default: $out .= " "; break;
                    }
                }
            } else { 
                if (ord($cd[1]) == 0x13) {
                    echo "@";
                    $skip = true;
                    continue;
                }
                $out .= "&#x".sprintf("%04x", $this->getShort(0, $cd)).";";
            }
        }

        return $out;
    }

    protected function getSomeBytes($data, $from, $count) {
        if ($data === null)
            $data = $this->data;

        $string = substr($data, $from, $count);
        if ($this->isLittleEndian)
            $string = strrev($string);

        return hexdec(bin2hex($string));
    }
    protected function getShort($from, $data = null) {
        return $this->getSomeBytes($data, $from, 2);
    }
    protected function getLong($from, $data = null) {
        return $this->getSomeBytes($data, $from, 4);
    }
}
?> 