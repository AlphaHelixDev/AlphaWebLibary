<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Test</title>
    </head>
    <body>
    <?php
        include "api/JsonDatabase.php";

        $db = null;

        MySQLAPI::register();

        foreach (MySQLAPI::getApis() as $api) {
            $api->initConnection();
            $db = new JsonDatabase(JsonDatabase::NUMBER, "alphaWeb", $api->database);
        }

        $db->setValue(0, "Bye!");

        echo $db->getValue(0);
    ?>
    </body>
</html>