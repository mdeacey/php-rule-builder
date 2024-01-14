<?php
session_start();


// echo "<h3>REQUEST</h3>";
// print_r($_REQUEST);

$show = $rulesVal = '';
if (isset($_REQUEST['cancel']) || isset($_REQUEST['deploy'])) {
    $_SESSION['rules'] = array();
    $_SESSION['rule_name'] = '';
}elseif (isset($_REQUEST['and']) || isset($_REQUEST['or']) || isset($_REQUEST['delete'])) {
    $rule_name = $_REQUEST['rule_name'];

    $fields = $_REQUEST['field'];
    $operators = $_REQUEST['operator'];
    $values = $_REQUEST['value'];
    $rule_values = $_REQUEST['rule_type'];

    $_SESSION['rule_name'] = $rule_name;
    $_SESSION['rules'] = array();
    foreach ($fields as $index => $field) {
        $_SESSION['rules'][$index]['rule_type'] = $rule_values[$index];
        $_SESSION['rules'][$index]['field'] = $fields[$index];
        $_SESSION['rules'][$index]['operator'] = $operators[$index];
        $_SESSION['rules'][$index]['value'] = $values[$index];
    }
    $rules = array_values($_SESSION['rules']);
    // print_r($_REQUEST);
    $add_rule = '';
    if (isset($_REQUEST['and'])) {
        $rule_no = $_REQUEST['and'];
        $add_rule = 'and';
    } elseif (isset($_REQUEST['or'])) {
        $rule_no = $_REQUEST['or'];
        $add_rule = 'or';
    }

    if ($add_rule == 'and' || $add_rule == 'or') {
        array_splice($rules, $rule_no[0] + 1, 0, array(array('rule_type' => $add_rule)));
    } elseif (isset($_REQUEST['delete'])) {
        $rule_no = $_REQUEST['delete'];
        if (array_key_exists($rule_no[0], $rules)) {
            unset($rules[$rule_no[0]]);
            // echo " DELETED!";
        }
    }
    $_SESSION['rules'] = $rules;
}

if (!isset($_SESSION['rules']) || sizeof($_SESSION['rules']) < 1) {
    $_SESSION['rules'] = array(array('rule_type' => 'and'));
}

$rulesVal = $_SESSION['rule_name'];
$rules = array_values($_SESSION['rules']);


$i = 0;
foreach ($rules as $key => $rule) {
    $rule_type = $rule['rule_type'];
    $fieldsVal = $operatorsVal = $valuesVal = '';
    if (isset($rule['field'])) {
        $fieldsVal = $rule['field'];
        $operatorsVal = $rule['operator'];
        $valuesVal = $rule['value'];
    }


    $delete = '<div class="delete desktop-show">
        <button name="delete[]" value="' . $key . '"><img src="./img/close.png" alt="Delete"></button>
    </div>';
    if ($i == 0) {
        $show .= '';
        $delete = '';
    } elseif ($rule_type == 'and') {
        $show .= '<div class="rule rule-name">
            <span class="and_or_crumb and_crumb"> And </span>
        </div>';
    } elseif ($rule_type == 'or') {
        $show .= '<div class="rule rule-name">
            <span class="and_or_crumb or_crumb"> Or </span>
        </div>';
    }

    $show .= '<div class="rule ' . $rule_type . '_rule">
    <div class="delete mobile-show">
        <button name="delete[]" value="1"><img src="./img/close.png" alt="Delete"></button>
    </div>
    <div class="field">
        <input type="hidden" name="rule_type[]" value="'.$rule_type.'">
        <label>
            <p class="label">Field</p>
            <select name="field[]" id="field_'.$key.'" class="inp" value="'.$fieldsVal.'">
                <option selected value="">Select...</option>
                <option value="AS Num">AS Num</option>
                <option value="Cookie">Cookie</option>
                <option value="Country">Country</option>
                <option value="Continent">Continent</option>
                <option value="Hostname">Hostname</option>
                <option value="IP Source Address">IP Source Address</option>
                <option value="Referer">Referer</option>
                <option value="Request Method">Request Method</option>
                <option value="SSL/HTTPS">SSL/HTTPS</option>
                <option value="URI Full">URI Full</option>
                <option value="URI">URI</option>
                <option value="URI Path">URI Path</option>
                <option value="URI Query String">URI Query String</option>
                <option value="HTTP Version">HTTP Version</option>
                <option value="User Agent">User Agent</option>
                <option value="X-Forwarded-For">X-Forwarded-For</option>
                <option value="Client Certificate Verified">Client Certificate Verified</option>
                <option value="Known Bots">Known Bots</option>
                <option value="Threat Score">Threat Score</option>
                <option value="Verified Bot Category">Verified Bot Category</option>
                <option value="MIME Type">MIME Type</option>
                <option value="Header">Header</option>
            </select>
        </label>
        <script>document.getElementById("field_'.$key.'").value = "'.$fieldsVal.'";</script>
    </div>
    <div class="operator">
        <label>
            <p class="label">Operator</p>
            <select name="operator[]" id="operator_'.$key.'" class="inp">
                <option selected value="">Select item...</option>
                <option value="equals">equals</option>
                <option value="does not equal">does not equal</option>
                <option value="greater than">greater than</option>
                <option value="less than">less than</option>
                <option value="greater than or equal to">greater than or equal to</option>
                <option value="less than or equal to">less than or equal to</option>
                <option value="is in">is in</option>
                <option value="is not in">is not in</option>
            </select>
        </label>
        <script>document.getElementById("operator_'.$key.'").value = "'.$operatorsVal.'";</script>
    </div>
    <div class="value">
        <label>
            <p class="label">Value</p>
            <input type="text" class="inp" name="value[]" value="'.$valuesVal.'">
        </label>
    </div>
        <div class="and_or">
            <button class="and_or_btn and_btn" name="and[]" value="' . $key . '">And</button>
            <button class="and_or_btn or_btn" name="or[]" value="' . $key . '">Or</button>
        </div>
        ' . $delete . '
    </div>';
    $i++;
}
// echo '<pre>';
// print_r($_SESSION['rules']);
// echo '</pre>';
?>
<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Firewall Rules</title>
        <link rel="stylesheet" href="./style.css">
    </head>

    <body>
        <div class="container">
            <h1>Create Rule</h1>
            <form action="" method="post">
                <div class="rule-name">
                    <label>
                        <p class="label">Rule name (required)</p>
                        <input type="text" name="rule_name" value="<?php echo $rulesVal; ?>" class="inp">
                        <p class="label">Give your rule a descriptive name.</p>
                    </label>
                </div>
                <hr>
                <h4>If incoming requests matches...</h4>
                <div class="rules">
                    <?php echo $show; ?>
                </div>
                <div class="action">
                    <h4>Then take action...</h4>
                    <label>
                        <p class="label">Choose action</p>
                        <select name="action" class="inp">
                            <option value="" disabled selected>Select...</option>
                            <option value="Managed Challenge">Managed Challenge</option>
                            <option value="Block">Block</option>
                            <option value="JS Challenge">JS Challenge</option>
                            <option value="Skip">Skip</option>
                            <option value="Interactive Challenge">Interactive Challenge</option>
                        </select>
                    </label>
                </div>
                <div class="final-btns">
                    <button class="btn-final btn-cancel" name="cancel">Cancel</button>
                    <button class="btn-final btn-deploy" name="deploy">Deploy</button>
                </div>
            </form>
        </div>
    </body>

</html>