<?php

    Class Task {
        public $title;
        public $text;
        public $items;
        public $hidden;
        public $completed;

        public function __construct($hidden_, $completed_)
        {
            $this->completed = $completed_;
            $this->hidden = $hidden_;
            $this->items = array();
            
            if ($this->hidden == true && $this->completed == false)
            {
                $this->text = "Complete prior tasks to access this task";
            }
            else if ($this->hidden == true && $this->completed == true)
            {
                $this->text = "You have completed this task";
            }
        }

        public function __toString()
        {
            $ret = "";
            $ret .= '<div class="card mb-3">';
            $ret .= '<div class="card-body">';

            $ret .= '<h2 class="card-title h4 ';

            if ($this->hidden)
            {
                $ret .= 'text-muted';
            }

            $ret .= '">';

            $ret .= $this->title;

            if ($this->completed)
            {
                $ret .= "<span class='mdi mdi-checkbox-marked-circle ms-1'></span>";
            }

            $ret .= '</h2>';
            $ret .= '<p class="m-0 ';

            if ($this->hidden)
            {
                $ret .= 'text-muted';
            }

            $ret .= '">';

            $ret .= $this->text;

            $ret .= '</p>';

            if (!$this->hidden)
            {
                $ret .= '<ol class="m-0">';
                foreach ($this->items as $item)
                {
                    $ret .= $item;
                }
                $ret .= '</ol>';
            }

            $ret .= '</div></div>';

            return $ret;
        }
    }

?>
