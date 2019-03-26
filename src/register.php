<?php

    /**
     * Validates form input
     *
     * @param form array
     *
     * @return array errors
     */
    function formValidate(
        $form=array(),
        $country_options=array(),
        $where_heard_options=array(),
        $emails_used=array()
    ) {
        $errors = array();

        if(!strlenBetween($form['name'], 2, 50)) {
            $errors['name'] = 'Invalid name';
        }
        if(!isValidEmail($form['email'])) {
            $errors['email'] = 'Invalid email';
        }
        if(!isUniqueEmail($form['email'], $emails_used)) {
            $errors['email'] = 'Email allready used';
        }
        if(!isValidEmail($form['email'])) {
            $errors['email'] = 'Invalid email: '.$form['email'];
        }

        if(!strlenBetween($form['password'], 6, 20)) {
            $errors['password'] = 'Invalid password';
        }
        if($form['confirm_password'] !== $form['password']) {
            $errors['confirm_password'] = 'Passwords dont match';
        }
        if(!strlenBetween($form['address1'], 2, 80)) {
            $errors['address1'] = 'Invalid Address #1';
        }
        if(!empty($form['address2']) AND !strlenBetween($form['address2'], 2, 80)) {
            $errors['address2'] = 'Invalid Address #2';
        }
         if(!empty($form['address3']) AND !strlenBetween($form['address3'], 2, 80)) {
            $errors['address3'] = 'Invalid Address #3';
        }
        if(!strlenBetween($form['town_city'], 2, 30)) {
            $errors['town_city'] = 'Invalid town/city';
        }
        if(!strlenBetween($form['county_region'], 2, 30)) {
            $errors['county_region'] = 'Invalid county/region';
        }
        if(!isValidOption($form['country'], $country_options)) {
            $errors['country'] = 'Invalid country';
        }
        if(!isValidOption($form['where_heard'], $where_heard_options)) {
            $errors['where_heard'] = 'Invalid where/heard';
        }
        if(!$form['accept_terms']) {
            $errors['accept_terms'] = 'Please accept terms';
        }

        return $errors;
    }
    // validator helpers
    function strlenBetween($str='', $min=2, $max=80)
    {
        return strlen($str) > $min AND strlen($str) < $max;
    }
    function isValidEmail($email='')
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    function isUniqueEmail($email='', $emails_arr=array())
    {
        return !$emails_arr[$email];
    }
    function isValidOption($option='', $options_arr=array())
    {
        return $options_arr[$option];
    }

    /**
     * Preload all required data from db
     *
     * @return array data
     */
    function dataLoad() {
        /*$db = new PDO(
            'mysql:host=localhost;dbname=my_database;charset=utf8mb4',
            'my_username', 'my_password'
        );
        $stmt = $db->query('SELECT * FROM form_defaults');
        $data['form'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //$stmt = $db->query('SELECT * FROM contries');
        //$data['country_options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //$stmt = $db->query('SELECT * FROM where_heard');
        //$data['where_heard_options'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        */

        // MOCKED
        $data['form_defaults'] = array(
            'name' => 'Your Name',
            'email' => 'Email Address',
            'password' => '',
            'Confirm Password' => '',
            'address1' => '',
            'address2' => '',
            'address3' => '',
            'town_city' => 'city',
            'county_region' => 'region',
            'country' => '',
            'where_heard' => '',
            'where_heard_other' => '',
            'accept_terms' => false,
        );
        $data['country_options'] = array(
            'iceland' => 'Iceland',
            'poland' => 'Poland',
            'spain' => 'Spain',
            'sweden' => 'Sweden',
            'uk' => 'UK',
        );
        $data['where_heard_options'] = array(
            'friend' => 'From a friend',
            'ads' => 'Ads',
            'google' => 'Google search',
            'other' => 'Other'
        );
        $data['emails_used'] = array(
            'jakubmz@gmail.com' => true
        );

        return $data;
    }

    /**
     * Format errors - 1 per line
     *
     * @param errors array
     *
     * @return html with errors
     */
    function errorsFormat($errors=array()) {
        return implode("<br>", $errors);
    }

     /**
     * Save user
     *
     * @param form array
     *
     * @return bool success/error
     */
    function saveUser($form=array()) {
        try {
            $db = new PDO(
                'mysql:host=localhost;dbname=my_database;charset=utf8mb4',
                'my_username', 'my_password'
            );
            $sql = $db->query('
                INSERT INTO users
                VALUES (
                    :name,
                    :email,
                    :password,
                    :confirm_password,
                    :address1,
                    :address2,
                    :address3,
                    :town_city,
                    :county_region,
                    :country,
                    :where_heard,
                    :where_heard_other,
                    :accept_terms
            )');
            $stmt = $pdo->prepare($sql);
            $stmt->execute($form);
            return true;
        } catch (Exception $e) {
            // TODO log exception
            return false;
        }
    }

    $data = dataLoad();
    header('Content-type: text/html');
    $form = empty($_POST) ? $data['form_defaults'] : $_POST;
    $errors = formValidate(
        $form,
        $data['country_options'],
        $data['where_heard_options'],
        $data['emails_used']
    );

    if(empty($errors)) {
        $result = saveUser($form);
        if($result) { // success - redirect
            header('Location: success.php');
            exit;
        }
    }
?>


<!DOCTYPE html>
<html lang="en" xml:lang="en">
    <head>
        <title>Regbo.com</title>
        <style>
            label { margin:0 0 0 2em; }
            input { margin:0 0 1em 0; padding:0.4em; min-width:15em; }
            select { margin:1em; padding:0.6em; }
            .error { color:red; }
            input.error { border-color:red; }
            .result { color:grey; }
        </style>
    </head>
    <body>
    <h1>REGISTER</h1>
    <h2 class="error"><?php echo errorsFormat($errors)?></h2>
    <?php if(empty($errors)) : ?>
    <h2 class="result"><?php echo $result ? 'REGISTER SUCCESSFUL' : 'REGISTER FAILED'?></h2>
    <?php endif;?>
    <form method="POST" action="register.php">
        <label for="name">Name</label>
        <input
            id="name"
            class="<?php echo $errors['name'] ? 'error' : ''?>"
            type="text"
            name="name"
            value="<?php echo $form['name'] ?>" />
        <label for="email">Email</label>
        <input
            id="email"
            class="<?php echo $errors['email'] ? 'error' : ''?>"
            type="text"
            name="email"
            value="<?php echo $form['email'] ?>" /><br>
        <label for="password">Password</label>
        <input
            id="password"
            class="<?php echo $errors['password'] ? 'error' : ''?>"
            type="text"
            name="password"
            value="<?php echo $form['password'] ?>" />
        <label for="confirm_password">Confirm password</label>
        <input
            id="confirm_password"
            class="<?php echo $errors['confirm_password'] ? 'error' : ''?>"
            type="text"
            name="confirm_password"
            value="<?php echo $form['confirm_password'] ?>" /><br>
        <label for="address1">Address1</label>
        <input
            id="address1"
            class="<?php echo $errors['address1'] ? 'error' : ''?>"
            type="text"
            name="address1"
            value="<?php echo $form['address1'] ?>" />
        <label for="address2">Address2</label>
        <input
            id="address2"
            class="<?php echo $errors['address2'] ? 'error' : ''?>"
            type="text"
            name="address2"
            value="<?php echo $form['address2'] ?>" />
        <label for="address3">Address3</label>
        <input
            id="address3"
            class="<?php echo $errors['address2'] ? 'error' : ''?>"
            type="text"
            name="address3"
            value="<?php echo $form['address3'] ?>" /><br>
        <label for="town_city">Town/City</label>
        <input
            id="town_city"
            class="<?php echo $errors['town_city'] ? 'error' : ''?>"
            type="text"
            name="town_city"
            value="<?php echo $form['town_city'] ?>" />
        <label for="county_region">County/Region</label>
        <input
            id="county_region"
            class="<?php echo $errors['county_region'] ? 'error' : ''?>"
            type="text"
            name="county_region"
            value="<?php echo $form['county_region'] ?>" />
        <label for="country">Country</label>
        <select
            id="country"
            class="<?php echo $errors['country'] ? 'error' : ''?>"
            name="country">
            <?php foreach($data['country_options'] as $k => $v) : ?>
            <option value="<?php echo $k ?>" <?php echo $form['country'] == $k ? "selected='selected'" : ''?>><?php echo "$v"?></option>
            <?php endforeach; ?>
        </select><br>
        <label for="where_heard">Where heard</label>
        <select
            id="where_heard"
            class="<?php echo $errors['where_heard'] ? 'error' : ''?>"
            name="where_heard">
            <?php foreach($data['where_heard_options'] as $k => $v) : ?>
            <option value="<?php echo $k ?>" <?php echo $form['where_heard'] == $k ? "selected='selected'" : ''?>><?php echo "$v"?></option>
            <?php endforeach; ?>
        </select>
        <label for="where_heard_other">Where heard other</label>
        <input
            id="where_heard_other"
            class="<?php echo $errors['where_heard_other'] ? 'error' : ''?>"
            type="text"
            name="where_heard_other"
            value="<?php echo $form['where_heard_other'] ?>" /><br>
        <label for="accept_terms">Accept terms</label>
        <input
            id="accept_terms"
            class="<?php echo $errors['accept_terms'] ? 'error' : ''?>"
            type="checkbox"
            name="accept_terms"
            <?php echo !empty($form['accept_terms']) ? 'checked="checked"' : '' ?> /><br>
        <input
            id="submit"
            type="submit"
            value="Submit" />
    </form>
    </body>
</html>
