<?php

namespace Craft;


class ContactFormEmailStylePlugin extends BasePlugin {

    public function getVersion()
    {
        return '0.1';
    }

    public function getDeveloper()
    {
        return 'Flexify';
    }

    public function getDeveloperUrl()
    {
        return 'https://www.flexify.net';
    }

    public function compileMessageFromArray($format="text",$postedMessage) {
        return craft()->templates->render('contactformemailstyle/body' . ucfirst($format) . '.html', array(
            'postedMessage' => $postedMessage
        ));
    }

    public function init() {
        craft()->on('contactForm.beforeCompile', function(ContactFormMessageEvent $event) {
            $postedMessage = $event->params['postedMessage'];
        
            if (is_array($postedMessage))
            {
                // Capture all of the message fields on the model in case there's a validation error
                $event->messageFields = $postedMessage;

                // Capture the original message body
                if (isset($postedMessage['body']))
                {
                    // Save the message body in case we need to reassign it in the event there's a validation error
                    $savedBody = $postedMessage['body'];
                }

                // If it's false, then there was no messages[body] input submitted.  If it's '', then validation needs to fail.
                if ($savedBody === false || $savedBody !== '')
                {
                    $event->message = $this->compileMessageFromArray("text", $postedMessage);
                    $event->htmlMessage = $this->compileMessageFromArray("html", $postedMessage);
                }
            }
            else
            {
                $event->message = $postedMessage;
                $event->messageFields = array('body' => $postedMessage);
            }

        });
    }

}
