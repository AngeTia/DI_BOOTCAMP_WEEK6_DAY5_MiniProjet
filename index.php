<?php
$state = [
    ['', '', ''],
    ['', '', ''],
    ['', '', ''],
];

$player = 'X';
// Select actif line
$activeCell = [0 => 0, 1 => 0];

// give game area
function renderGame($state, $activeCell, $player)
{
    $output = '';
    $output .= 'Player:' . $player . "\n";
    foreach ($state as $x => $line) {
      $output .= '|';
      foreach ($line as $y => $item) {
        switch ($item) {
          case '';
            $cell = ' ';
            break;
          case 'X';
            $cell = 'X';
            break;
          case 'O';
            $cell = 'O';
            break;
          default:
            return "none";
        }
        if ($activeCell[0] == $x && $activeCell[1] == $y) {
          $cell = '-'. $cell . '-';
        } else {
          $cell = ' ' . $cell . ' ';
        }
        $output .= $cell . '|';
      }
      $output .= "\n";
    }
    return $output;
  }

// Function to save keywoard value
function translateKeyPress($string)
{
    switch ($string) {
      case "\033[A":
        return "UP";
      case "\033[B":
        return "DOWN";
      case "\033[C":
        return "RIGHT";
      case "\033[D":
        return "LEFT";
      case "\n":
        return "ENTER";
      case " ":
        return "SPACE";
      case "\010":
      case "\177":
        return "BACKSPACE";
      case "\t":
        return "TAB";
      case "\e":
        return "ESC";
      default:
        return $string;
     }
}

// Function to move in game
function move($stdin, &$state, &$activeCell, &$player)
{
    $key = fgets($stdin);
    if ($key) {
      $key = translateKeyPress($key);
      switch ($key) {
        case "UP":
          if ($activeCell[0] >= 1) {
            $activeCell[0]--;
          }
          break;
        case "DOWN":
          if ($activeCell[0] < 2) {
            $activeCell[0]++;
          }
          break;
        case "RIGHT":
          if ($activeCell[1] < 2) {
            $activeCell[1]++;
          }
          break;
        case "LEFT":
          if ($activeCell[1] >= 1) {
            $activeCell[1]--;
          }
          break;
        case "ENTER":
        case "SPACE":
          if ($state[$activeCell[0]][$activeCell[1]] == '') {
            $state[$activeCell[0]][$activeCell[1]] = $player;
            if ($player == 'X') {
              $player = 'O';
            } else {
              $player = 'X';
            }
          }
          break;
        default:
          return "none";
       }
    }
  }
  
//To determine the winner
function isWinState($state)
{
    foreach (['X', 'O'] as $player) {
        // To verify line
        foreach ($state as $x => $line) {
        
        if ($state[$x][0] == $player && $state[$x][1] == $player && $state[$x][2] == $player) {
          die($player . 'wins');
        }
        // To verify colum
        foreach ($line as $y => $item) {
          if ($state[0][$y] == $player && $state[1][$y] == $player && $state[2][$y] == $player) {
            die($player . 'wins');
          }
        }
      }
      if ($state[0][0] == $player && $state[1][1] == $player && $state[2][2] == $player) {
        die($player . ' wins');
      }
      if ($state[2][0] == $player && $state[1][1] == $player && $state[0][2] == $player) {
        die($player . ' wins');
      }
    }
    // Verify egality
    $blankQuares = 0;
    foreach ($state as $x => $line) {
      foreach ($line as $y => $item) {
        if ($state[$x][$y] == '') {
          $blankQuares++;
        }
      }
    }
    if ($blankQuares == 0) {
      die('DRAW!');
    }
}
// Update data in command line
$stdin = fopen('php://stdin', 'r');
stream_set_blocking($stdin, 0);
system('stty cbreak -echo');
while (1) {
  system('clear');
  move($stdin, $state, $activeCell, $player);
  echo renderGame($state, $activeCell, $player);
  isWinState($state);
}