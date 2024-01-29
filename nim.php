<?php
    /**
     * I Justin Donaldson, 000900940, certify that this material is my original work. No other person's work has been used without suitable acknowledgment and I have not made my work available to anyone else.
     * @author: Justin Donaldson
     * @version: 202335.00
     * @package: COMP 10260 Assignment 3
     */
    session_start();

    //Everytime the Get Is submitted, it updates the game state. Therefore we a just returning the state variables?
    if(isset($_GET)){
        //Variables
        $mode =         filter_input(INPUT_GET, 'mode',           FILTER_VALIDATE_INT); // Optional
        $difficulty =   filter_input(INPUT_GET, 'difficulty',     FILTER_VALIDATE_INT); // 0 - Random, 1 - Optimal Play
        $count =        filter_input(INPUT_GET, 'count',          FILTER_VALIDATE_INT); // The current count of stones used in the game (Default 20)
        $player_move =  filter_input(INPUT_GET, 'player_move',    FILTER_VALIDATE_INT); // The number of stones the player is removing [Only on their turn]
        
        //If the session is not set, then we initialize the starting values
        if(!isset($_SESSION['player'])){
            //echo "Session not set";
            $_SESSION['count'] = 20;
            $_SESSION['player'] = "player";
            $_SESSION['difficulty'] = $difficulty;
            $_SESSION['mode'] = $mode;
        }
        else if($mode == 0){
            //echo "Mode is set to zero" . $mode;
            //Reset the game
            $_SESSION['count'] = 20;
            $_SESSION['player'] = "player";
            $_SESSION['difficulty'] = $difficulty;
            $_SESSION['mode'] = $mode;
        }
        else if($_SESSION['count'] <= 0){
            //echo "Count is less than or equal to zero" . $_SESSION['count'];
            //Reset the game
            $_SESSION['count'] = 20;
            $_SESSION['player'] = "player";
            $_SESSION['difficulty'] = $difficulty;
            $_SESSION['mode'] = $mode;
        }


        //If this is the player's turn
        if($_SESSION['player'] === "player"){

            $remainingCount = $_SESSION['count'] - $player_move;
            $move = $player_move;

            //IF the remaining count is less than or equal to 0, then the player has lost the game
            if($remainingCount <= 0){
                //The game has ended and the player has lost
                $mode = 0;
                $winner = "The Player has lost the game";
            }
            else{
                //Else, the game is still in progress
                $winner = "Undetermined";
            }
        }

        if($_SESSION['player'] === "computer"){
            //Else begin executing the computer's moves
            if($difficulty != 1){ //Use a random move
                $move = random_int(1, 3);
                $remainingCount = $_SESSION['count'] - $move;
                //If the reamining is less than or equal to 0 - End the game.s

            }
            else if($difficulty == 1){ //Use the optimal move
                $move = GetOptimalMove($_SESSION['count']);
                $remainingCount = $_SESSION['count'] - $move;
            }

            if($remainingCount <= 0){
                $winner = "You have won the game";
            }
            else{
                $winner = "Undetermined";
            }
        }

        //Update the game state for the Client:
        $array = array("move" => $move, "stones" => $remainingCount,"player" => $_SESSION['player'], "winner" => $winner);
        
        //Encode and return the operations
        echo json_encode($array);

        //Update the session variables
        $_SESSION['count'] = $remainingCount;

        //Switch the player
        if($_SESSION['player'] === "player"){
            $_SESSION['player'] = "computer";
        }
        else{
            $_SESSION['player'] = "player";
        }

        $_SESSION['difficulty'] = $difficulty;
        $_SESSION['mode'] = $mode;
    }

    /**
     * To determine the optimal move - take the number of stones remaining Modulo 4 and pick a move that makes the result after you pick to be 1.
     * @param $count - The number of stones remaining
     * */
    function GetOptimalMove($count){
        $remaining = $count % 4;
        switch($remaining){
            case 0:
                $returnVal = 3;
                break;
            case 1:
                $returnVal =  random_int(1, 3);
                break;
            case 2:
                $returnVal =  1;
                break;
            case 3:
                $returnVal =  2;
                break;
        }
        return $returnVal;
    }

?>