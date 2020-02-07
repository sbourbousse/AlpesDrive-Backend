<?php
function somethingMissing() {
    $missing = false;
    foreach (func_get_args() as $param) {
        if ($param == null)
            $missing = true;
    }
    return $missing;
}

?>