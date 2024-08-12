<?php

function jsonResponse($data = null, $status = 200, $errors=null, $message=null) {

    return response()->json(compact("data", "message", "errors"), $status);
}
