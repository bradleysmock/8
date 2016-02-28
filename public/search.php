<?php

    require(__DIR__ . "/../includes/config.php");

    // numerically indexed array of places
    $places = [];
    
    // search database for places matching $_GET["geo"]
    // GEO might be city, state, or zip
    
    if (($input = $_GET["geo"]) !== NULL)
    {
        // $input == POSTAL CODE
        if (is_numeric($input))
        {
            $places = query("SELECT * FROM `places` WHERE
                        postal_code = ?", $input);
        }
        
        // $input == CITY, STATE
        else if (strpos($input, ',') !== FALSE)
        {
            list($city, $state) = explode(',', $input);
            
            $places = query("SELECT * FROM `places` WHERE 
                        place_name = ? AND
                        (admin_name1 = ? OR
                        admin_code1 = ?)", $city, trim($state), trim($state)
                        );     
        }
        
        // $input is other format
        else
        {
            // split by SP if exist
            $parts = explode(' ', $input);
            
            foreach ($parts as $part)
            {
                $places += query("SELECT * FROM `places` WHERE 
                        MATCH(place_name, admin_name1, admin_code1)
                        AGAINST(?)", $part
                        );
            }
        }
     }
        
    // HINTS
    // like, match
    
    // output places as JSON (pretty-printed for debugging convenience)
    header("Content-type: application/json");
    print(json_encode($places, JSON_PRETTY_PRINT));

?>
