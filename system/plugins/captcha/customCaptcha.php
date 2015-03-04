<?php

class SJB_CustomCaptcha extends Zend_Captcha_Image
{
    public function render(Zend_View_Interface $view = null, $element = null)
    {
        return '
        <img id="customCaptcha" width="' . $this->getWidth() . '" height="' . $this->getHeight() . '" alt="' . $this->getImgAlt()
             . '" src="' . $this->getImgUrl() . $this->getId() . $this->getSuffix() . '" />
        <input type="hidden" name="captcha[id]" value="'.$this->getId().'" />';
    }
}