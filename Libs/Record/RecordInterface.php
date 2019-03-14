<?php

interface RecordBehavior{
    public function create();
    public function update();
    public function setFields($updateObj);
}