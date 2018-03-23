<?php

class ParseExtended extends Parsedown
{

    protected function blockHeader($Line)
    {
      if (isset($Line['text'][1]))
      {
          $level = 1;

          while (isset($Line['text'][$level]) and $Line['text'][$level] === '#')
          {
              $level ++;
          }

          if ($level > 6)
          {
              return;
          }

          $text = trim($Line['text'], '# ');

          $Block = array(
              'element' => array(
                  'name' => 'p',
                  'text' => $text,
                  'handler' => 'line',
              ),
          );

          return $Block;
      }
    }

    protected function blockSetextHeader($Line, array $Block = null)
    {
        return;
    }

}
