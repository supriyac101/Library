<?php
    namespace Custom\Mobileapp\Api;
     
    interface HomeInterface
    {
        /**
        * Returns greeting message to user
        *
        * @api
        * @param json.
        * @return json Greeting message with json.
        */
        
        public function homepage();
    }