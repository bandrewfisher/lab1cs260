<!DOCTYPE html>
<html>
	<head>
		<title>Checkers!</title>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js">
		</script>		
		<style>
			div#board {
				display: grid;
				width: 500px;
				height: 500px;
				grid-template-rows: 62.5px 62.5px 62.5px 62.5px 62.5px 62.5px 62.5px 62.5px;
				grid-template-columns: 62.5px 62.5px 62.5px 62.5px 62.5px 62.5px 62.5px 62.5px;
				border: 2px solid black;
			}
			div.black {
				background-color: black;
			}
			div.white {
				background-color: beige;
			}
			div.checker {
				height: 40px;
				width: 40px;
				border-radius: 50%;
				margin: 12px;
			}
			.red_checker {
				background-color: red;
			}
			.white_checker {
				background-color: white;
			}
			#action-panel {
				float: left;
				width: 200px;
				height: 200px;
				background-color: beige;
				margin-left: 20px;
				border: 2px solid black;
				border-radius: 15px;
				padding: 10px;
			}
			#board-wrapper {
				float: left;
				display: inline-block;
			}
		</style>
	</head>
	<body>
	    <h3>Checkers by Brett Fisher</h3>
		<div id="board-wrapper">
			<div id="board">
			</div>
		</div>
		<div id="action-panel">
			<p>Red pieces taken: <label id="red_taken"></label></p>
			<p>White pieces taken: <label id="white_taken"></label></p>
			<p><strong><label id="turn_label"></label></strong></p>
			<button id="new_game">New Game</button>
		</div>
		
		<script>
		//GLOBALS
		var board=[];		//Holds Checker objects and where they are on the board
		var selChecker = null;	//Which checker has been selected by the user (Checker object)
		var turn = "red";	//Whose turn is it
		
		//For calculating possible moves of a checker.
		var NW = [-1, -1];
		var NE = [-1, 1];
		var SW = [1, -1];
		var SE = [1, 1];
		
		//How many pieces each player has. Game over when one reaches 0
		var redPieces = 12;
		var whitePieces = 12;
		var gameOver = false;
		
		//Who is the winner - red or white
		var winner;
		
		//Check if a jump is being repeated
		var repeatJump = false;
		
		class Checker {
			constructor(color, row, col, rank="normal") {
				this.color = color;
				this.row = row;
				this.col = col;
				this.rank = rank;	//Becomes "king" when a checker reaches the other side
			}
			
			setCoords(row, col) {
				this.row = row;
				this.col = col;
			}
		}
		
		//For calculating valid moves for a checker.
		class Move {
			constructor(row, col, jump=false) {
				this.row = row;
				this.col = col;
				this.jump = jump;	//True if the checker can jump
			}
		}
		
		
		/*****DIGITAL ARRAY FUNCTIONS*******************/
		
		//Check if coordinates are in bounds on the board
		function inBounds(row, col) {
			if((0<=row && row<8) && (0<=col && col<8)) {
				return true;
			}
			return false;
		}
		//Return true if there are jump moves available for a color
		function jumpAvailable(color) {
			
			for(var i=0; i<8; i++) {
				for(var j=0; j<8; j++) {
					if(board[i][j] != null) {
						var checker = board[i][j];
						if(checker.color == color) {
							var moves = getValidMoves(i, j);
							for(var k=0; k<moves.length; k++) {
								if(moves[k].jump) {
									canJump = true;
									return true;
								}
							}
						}
					}
				}
			}
			return false;
		}
		//Return an array of valid moves for checker at row, col
		function getValidMoves(row, col) {
			var checker = board[row][col];
			var validMoves = [];
			if(checker != null) {
				var moveDirs;	//Which directions the checker can move
				if(checker.rank == "king") {
					moveDirs = [NW, NE, SW, SE];
				} else {
					if(checker.color == "red") {
						moveDirs = [NW, NE];
					} else {
						moveDirs = [SW, SE];
					}
				}
				
				for(var i=0; i<moveDirs.length; i++) {
					//Calculate where the checker can move
					var moveRow = row + moveDirs[i][0];
					var moveCol = col + moveDirs[i][1];
					
					//If it's in bounds and not occupied, add it to possible moves
					if(inBounds(moveRow, moveCol)) {
						if(board[moveRow][moveCol] == null) {
							validMoves.push(new Move(moveRow, moveCol));
						}
						//If not empty, check if there are available jump moves
						else {
							if(checker.color != board[moveRow][moveCol].color) {
								var jumpRow = moveRow + moveDirs[i][0];
								var jumpCol = moveCol + moveDirs[i][1];
								if(inBounds(jumpRow, jumpCol)) {
									if(board[jumpRow][jumpCol] == null) {
										validMoves.push(new Move(jumpRow, jumpCol, true));
									}
								}
							}
						}
					}
				}
				
			}
			return validMoves;
		}
		
		/**************************************************/
		
		
		
		/******VISUAL FUNCTIONS*******************/
		//Return an HTML tag for a square by its row and column
		function getSquare(row, col) {
			return $("div.square[data-row='"+row+"'][data-col='"+col+"']");
		}
		
		//Add a click listener to the checkers.
		//Called at initialization and when a new checker is added.
		function setCheckerListener(selector) {
			$(selector).click(function() {
				reset();
									
				var row = parseInt($(this).attr("data-row"));
				var col = parseInt($(this).attr("data-col"));
				selChecker = board[row][col];
				if(turn == selChecker.color) {	
					var validMoves = getValidMoves(row, col);
					var jump = jumpAvailable(selChecker.color);
						
						
					for(var i=0; i<validMoves.length; i++) {
						if(jump && !validMoves[i].jump) {
							continue;
						}
						var moveRow = validMoves[i].row;
						var moveCol = validMoves[i].col;
						
						var moveSquare = getSquare(moveRow, moveCol);
						moveSquare.addClass("selected");
						moveSquare.css("background-color", "green");
					}	
				}
			});
		}
		
		//Reset square colors
		function reset() {
			$(".selected").css('background-color', 'black');
			$(".selected").attr("class", "square black");
		}
		
		//Update the visual board by reading the "board" array
		function updateVisualBoard() {
			for(var i=0; i<8; i++) {
				for(var j=0; j<8; j++) {
					var square = getSquare(i, j);
					if(board[i][j] != null) {	
						//Check if the square has a checker on it.
						//It will not if the player moved a checker onto it, so put one on
						if(square.children().length == 0) {
						var color = board[i][j].color;
							square.append("<div class='checker "+color+"_checker' " +
								"data-row='"+i+"' data-col='"+j+"'></div>");
							if(board[i][j].rank=="king") {
								if(board[i][j].color == "red") {
									square.children(":first").css("border", "6px solid yellow");
									square.children(":first").css("margin", "5px");
								} else {
									square.children(":first").css("border", "6px solid red");
									square.children(":first").css("margin", "5px");
								}
							}
						}
					} else {
						//The player moved a checker away from the square,
						//So take it off the visual display
						if(square.children().length > 0) {
							square.empty();
							
						}
					}
				}
				if(!gameOver) {
					var makeJump = "";
					if(jumpAvailable(turn)) {
						makeJump = " (Jump required!)";
					}
					if(turn == "red") {
						$("#turn_label").text("Red's turn"+makeJump);
					} else {
						$("#turn_label").text("White's turn"+makeJump);
					}
				} else {
					if(winner == "red") {
						$("#turn_label").text("Red Wins!");
					} else {
						$("#turn_label").text("White Wins!");
					}
				}
			}
			
			//Update the action panel to show how many pieces have been taken
			$("label#red_taken").text(12-redPieces);
			$("label#white_taken").text(12-whitePieces);
			
			
			if(!repeatJump) {
				reset();
			}
		}
		
		function initNewGame() {
			//Initialize array representation of board with nulls
			while(board.length) {
				board.pop();
			}
			
			$('.square').empty();
			turn = "red";
			gameOver = false;
			for(var i=0; i<8; i++) {
				board.push([]);
				for(var j=0; j<8; j++) {
					board[i].push(null);
				}
			}
			
			//The squares the checkers should be on for player 1 and 2
			var p1Squares = [[7, 0], [7, 2], [7, 4], [7, 6],
				[6, 1], [6, 3], [6, 5], [6, 7],
				[5, 0], [5, 2], [5, 4], [5, 6] ];
			var p2Squares = [[0, 1], [0, 3], [0, 5], [0, 7],
				[1, 0], [1, 2], [1, 4], [1, 6],
				[2, 1], [2, 3], [2, 5], [2, 7]];
			//var p1Squares = [[2, 3], [7, 0], [7, 2]];
			//var p2Squares = [[1, 4], [1, 6], [3, 6]];
			
			//Place new Checker objects in the board array
			for(var i=0; i<12; i++) {
				var row1 = p1Squares[i][0];
				var col1 = p1Squares[i][1];		
				var row2 = p2Squares[i][0];
				var col2 = p2Squares[i][1];
				board[row1][col1] = new Checker("red", row1, col1);
				board[row2][col2] = new Checker("white", row2, col2);
			}
			
			updateVisualBoard();	//Place the checkers on the visual board
			setCheckerListener('.checker');
			
		}
		/***********************************************/
		
		$(document).ready(function() {
			//Print out visual board
			for(var i=0; i<8; i++) {
				for(var j=0; j<8; j++) {
					//Calculate the square color
					var color = "white";
					if((i%2 == 1 && j%2==0) ||
						(i%2==0 && j%2==1)) {
						color = "black";
					}					
					$('#board').append("<div class='square " + color +
						"' data-row='" + i + "' data-col='" + j + "'></div>");
				}
			}
			
			initNewGame();
			
			$('#new_game').click(function() {
				initNewGame();
			});
			
			$('.square').click(function() {
				var sqRow = $(this).attr("data-row");
				var sqCol = $(this).attr("data-col");
				
				
				//The user is trying to move a checker
				if($(this).attr("class").includes("selected")) {
					
					var validMoves = getValidMoves(selChecker.row, selChecker.col);
					repeatJump = false;
					for(var i=0; i<validMoves.length; i++) {
						var moveRow = validMoves[i].row;
						var moveCol = validMoves[i].col;
						if(moveRow == sqRow && moveCol == sqCol) {
							var rank = selChecker.rank;
							var color = selChecker.color;
							if((selChecker.color == "white" && moveRow==7)
								|| (selChecker.color == "red" && moveRow==0)) {
									rank = "king";
							}
							board[moveRow][moveCol] = new Checker(selChecker.color, moveRow,
								moveCol, rank);
							board[selChecker.row][selChecker.col] = null;
							
							//If jumped over another checker, get rid of it
							if(validMoves[i].jump) {
								var jumpedRow = (selChecker.row+moveRow)/2;
								var jumpedCol = (selChecker.col+moveCol)/2;
								
								if(board[jumpedRow][jumpedCol].color == "red") {
									redPieces--;
									if(redPieces == 0) {
										gameOver = true;
										winner = "white";
									}
								} else {
									whitePieces--;
									if(whitePieces == 0) {
										gameOver = true;
										winner = "red";
									}								
								}
								board[jumpedRow][jumpedCol] = null;
								//Check if there are more jump moves for that checker.
								//If there are, the player has to move that checker again.
								var repeatMoves = getValidMoves(moveRow, moveCol);
								console.log(repeatMoves);
								for(var i=0; i<repeatMoves.length; i++) {
									if(repeatMoves[i].jump) {
										repeatJump = true;
										selChecker = new Checker(color, moveRow, moveCol, rank);
										//selChecker.row = moveRow;
										//selChecker.col = moveCol;
										console.log("Ok, changing square CSS");
										var moveSquare = getSquare(repeatMoves[i].row, repeatMoves[i].col);
										console.log(moveSquare);
										reset();
										moveSquare.addClass("selected");
										moveSquare.css("background-color", "green");
										break;
									}
								}
							}
							
							
							if(!repeatJump) {						
								//Change turns
								if(turn == "red") {
									turn = "white";
								} else {
									turn = "red";
								}
							}
							
							updateVisualBoard();
							setCheckerListener(".checker[data-row='"+moveRow+"'][data-col='"+moveCol+"']");
							break;
						}
					}
					
				} else if (board[sqRow][sqCol] == null) {
					reset();
				}
				
				
			});
			//setCheckerListener('.checker');
			
		});
			
		</script>
	</body>
</html>