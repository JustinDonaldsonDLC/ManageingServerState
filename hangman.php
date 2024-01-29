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
        $mode = filter_input(INPUT_GET, 'mode', FILTER_SANITIZE_SPECIAL_CHARS); // Optional
        $letter = filter_input(INPUT_GET, 'letter', FILTER_SANITIZE_SPECIAL_CHARS); 
        
        //If the session is not set, then we initialize the starting values
        if(!isset($_SESSION['word'])){
            ResetGameSession($letter);
        }
        else if ($mode == "reset"){
            ResetGameSession($letter);
        }


        $result = false; //Used to track if the letter is in the word

        //Loop through the string and check if the letter is in the word (Was unsure if i could use Array_search or in_array for this part)
        for($i = 0; $i < strlen($_SESSION['word']); $i++){
            if($_SESSION['word'][$i] === $letter){
                $result = true;

            }
        }

        //Update the Alphabet & Guesses
        if(in_array($letter, $_SESSION['alphabet'])){
            $key = array_search($letter, $_SESSION['alphabet'], true);
            array_splice($_SESSION['alphabet'], $key, 1);
            array_push($_SESSION['guesses'], $letter);
        }

        //Update the secret Word
        $secretWord = "";
        for($i = 0; $i < strlen($_SESSION['word']); $i++){
            if(in_array($_SESSION['word'][$i], $_SESSION['guesses'])){
                $secretWord .= $_SESSION['word'][$i];
            }
            else{
                $secretWord .= "-";
            }
        }

        //If the result is false, then the player has made an incorrect guess
        if($result === false){
            //Increment the strikes if the mode is not being reset and the strikes are lower than 7
            if($mode != "reset" && $_SESSION['strikes'] < 7)
                $_SESSION['strikes'] += 1;          
        }
        //If the sercret word has been guessed below 7 strikes, then the player has won
        if($secretWord === $_SESSION['word'] && $_SESSION['strikes'] < 7){
            $_SESSION['status'] = "You Win!";
        }
        //If the strikes are greater than or equal to 7, then the player has lost
        else if($_SESSION['strikes'] >= 7){
            $_SESSION['status'] = "You Lose!";
            $secretWord = $_SESSION['word'];
        }

        //Sort the Guesses & Alphabet
        sort($_SESSION['guesses']);
        sort($_SESSION['alphabet']);

        //Return the JSON Array with the required variables
        $array = array("guesses" => $_SESSION['guesses'], "alphabet" => $_SESSION['alphabet'], "secret" => $secretWord, "strikes" => $_SESSION['strikes'], "status" => $_SESSION['status']);
        echo json_encode($array);
    }

    /**
     * Resets the game session
     * @param $letter - The letter that was guessed by the client
     * */
    function ResetGameSession($letter){
        //Get a word from the wordlist text file
        $wordlist = file("wordlist.txt");
        //Set a random number between 0 and the length of the wordlist
        $index = random_int(0, count($wordlist) - 1);

        //Check if the index is set
        if(isset($_SESSION['index'])){
            //If the index is set, then we need to make sure that the index is not the same as a previously selected index
            while(in_array($index, $_SESSION['index'])){
                $index = random_int(0, count($wordlist) - 1);
            }
            //Add the index to the array
            array_push($_SESSION['index'], $index);
        }
        else{
            //If the index is not set, then we need to initialize the array
            $_SESSION['index'] = array($index);
        }

        //Get the word from the wordlist
        $word = $wordlist[$index];
        //Trim the word (in case of whitespace)
        $word = trim($word);
        

        //Initialize the session variables
        if($letter != null)
            $_SESSION['guesses'] = array($letter);
        else
            $_SESSION['guesses'] = [];
        //Initialize the alphabet
        $_SESSION['alphabet'] = array("a", "b", "c", "d", "e", "f", "g", "h", "i", 
                                        "j", "k", "l", "m", "n", "o", "p", "q", "r", 
                                        "s", "t", "u", "v", "w", "x", "y", "z");
        $_SESSION['word'] = $word; //Store the selected word in the session
        $_SESSION['secret'] = ""; //Initialize the secret word
        $_SESSION['strikes'] = 0; //Initialize the strikes
        $_SESSION['status'] = "Game in progress"; //Initialize the status
    }
?>