{capture name="wysiwygName"}{$id}{/capture}
{capture name="wysiwygClass"}inputText{/capture}
{assign var='wysiwygType' value='none'}
                                                         
{WYSIWYGEditor name=$smarty.capture.wysiwygName class=$smarty.capture.wysiwygClass width="40%" height="100" type=$wysiwygType value=$value conf="BasicAdmin"}