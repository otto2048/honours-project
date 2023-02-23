#include &lt;iostream&gt;
#include &lt;string&gt;
#include "dartboard.h"
#include "player.h"
#include "throwStrategy.h"
#include &lt;cstdlib&gt; //random numbers header file
#include &lt;ctime&gt; //get date and time info
#include &lt;iomanip&gt; //for precision
using namespace std;

int main()
{
	//variables
	int points = 0, counter = 0, b = 2, aimFor = 0;
	int games = 10000; //number of matches
	string typeOf = " ";

	bool selected = false;
	bool canDoRisk = false; //checking whether a quicker out is possible
	bool aim = false; //for maintaining strategy
	int check = 0;

	int joepoints = 0, sidpoints = 0, setup = 0; //determining who goes first in a set
	char result = ' ';
	int game = 1;

	srand(time(0)); //initialise random number generator with time

	//calculating frequencies of scores:

	int place = 0, y = 0;
	int possibleFrequency[3][14]; //array to show the percentage of each score outcome

	for (int i = 0; i < 7; i++)
	{
		possibleFrequency[0][i] = 0;
		possibleFrequency[1][i] = i; //scores joe could lose with
		possibleFrequency[2][i] = 7; //sid wins
	}

	for (int i = 7; i < 14; i++)
	{
		possibleFrequency[0][i] = 0;
		possibleFrequency[1][i] = 7; //joe wins
		possibleFrequency[2][i] = y; //scores sid could lose with
		y++;
	}

	//function prototypes:

	string evenORodd(int);
	int determineTypeWithWinStrat(player&, throwStrategy, int); //works out what type of throw to use with the points player is aiming for in their strategy
	void checkBelowTwo(player&); //checks if players score has gone below 2
	void setForThreeDarts(player&, dartboard); //sets players strategy with three darts
	void setForTwoDarts(player&, dartboard); //sets players strategy with two darts

	//create objects
	player joe("Joe", 71, 501);
	joe.setThrows(0);
	joe.setSetsWon(0);
	joe.setGamesWon(0);

	player sid("Sid", 73, 501);
	sid.setThrows(0);
	sid.setSetsWon(0);
	sid.setGamesWon(0);

	throwStrategy throwType;

	dartboard board;

	for (int a = 0; a < 20; a++) //initialising double values of the dartboard
	{
		board.setDoubleValues(b, a);
		b = b + 2;
	}

	b = 3;

	for (int a = 0; a < 20; a++) //initialising treble values of the dartboard
	{
		board.setTrebleValues(b, a);
		b = b + 3;
	}

	for (int z = 0; z < games; z++)
	{
		while (joe.getSetsWon() != 7 && sid.getSetsWon() != 7) //championship loop
		{
			//closes to the bull to determine who goes first
			do
			{
				joepoints = throwType.bullThrow(joe.getSuccessRate());
				sidpoints = throwType.bullThrow(sid.getSuccessRate());

				if (joepoints > sidpoints) //joe has first throw in game one
				{
					result = 'J';
					joe.setThrows(0); //if this is second time darts have been thrown to determine who goes first
				}

				else
				{
					result = 'S';
					joe.setThrows(3);
				}

			} while (joepoints == sidpoints); //repeat until one player scores higher

			while (setup == 0) //ensures that on the first game of the first set, Sid will go first
			{
				result = 'S';
				joe.setThrows(3);
				setup++;
			}

			while (joe.getGamesWon() != 3 && sid.getGamesWon() != 3)  //set loop
			{
				while (game == 1) //game loop
				{
					//joe's turn

					while (joe.getScore() > 230 && joe.getThrows() < 3) //aim for 60
					{
						points = throwType.trebleThrow(joe.getSuccessRate(), 20); //store result of throw in points
						counter = joe.standardThrow(counter, points); //add one to throws, save last throw, set new score
					}

					while (joe.getScore() <= 230 && joe.getScore() > 170 && joe.getThrows() < 3) //targeting for a finish
					{
						if (joe.getThrows() == 2 && sid.getScore() >= 230) //joe has one throw left and is winning, can take possibly less risky route to get out on the next round
						{
							aimFor = joe.getScore() - 170; //170 is the highest number a player can get out at
							b = 1;

							//check if aim for is a treble/double/single
							for (int a = 0; a < 20; a++)
							{
								if (aimFor == b)
								{
									typeOf = "single";
									selected = true;
									break; //stop looking through loop once a match is found
								}
								b++;
							}

							if (selected == false) //points to aim for has already been decided
							{
								for (int a = 0; a < 20; a++)
								{
									if (aimFor == board.getDoubleValues(a))
									{
										typeOf = "double";
										selected = true;
										aimFor = board.getDoubleValues(a) / 2;
										break;
									}
								}
							}

							if (selected == false)
							{
								for (int a = 0; a < 20; a++)
								{
									if (aimFor == board.getTrebleValues(a))
									{
										typeOf = "treble";
										selected = true;
										aimFor = board.getDoubleValues(a) / 3;
										break;
									}
								}
							}

							if (selected == false) //with the current score, player can't get to 170 with any specific shot
							{
								typeOf = "treble";
								aimFor = 20;
							}

							//with the amount player is aiming for decided, can work out what kind of shot it is
							if (typeOf == "treble")
							{
								points = throwType.trebleThrow(joe.getSuccessRate(), aimFor);
								counter = joe.standardThrow(counter, points);
							}
							else if (typeOf == "double")
							{
								points = throwType.doubleThrow(aimFor);
								counter = joe.standardThrow(counter, points);
							}
							else {
								points = throwType.singleThrow(aimFor);
								counter = joe.standardThrow(counter, points);
							}
						}

						else if (joe.getThrows() == 2 && sid.getScore() <= 230) //joe has one dart left and is losing, take more risky route (ie always a treble) to get out on the next round
						{
							points = throwType.trebleThrow(joe.getSuccessRate(), 20);
							counter = joe.standardThrow(counter, points);
						}

						else //winning or losing, with any other amount of darts left, joe should go for the highest possible score as there is a chance he could get out on this round
						{
							points = throwType.trebleThrow(joe.getSuccessRate(), 20);
							counter = joe.standardThrow(counter, points);
						}
					}

					while (joe.getScore() <= 170 && joe.getThrows() < 3 && joe.getScore() > 0)
					{
						while (aim == false && joe.getThrows() < 3)
						{
							if (joe.getThrows() == 0 && sid.getScore() > 170) //joe has three darts and is winning
							{
								setForThreeDarts(joe, board);
								aim = true;
							}

							if (joe.getThrows() == 0 && sid.getScore() <= 170) //joe has three darts and is losing
							{
								setForTwoDarts(joe, board);
								if (joe.getWinStrategy(1) != 0)
								{
									canDoRisk = true; //joe can take a riskier route to get out quicker
								}

								if (canDoRisk == false) //joe will have to go for a 'normal route' using three darts
								{
									setForThreeDarts(joe, board);
								}
								aim = true;
							}

							//winning or losing, with two darts left joe will always take a riskier route if necessary, && makes sure a route hasnt already been chosen with two throws
							if (joe.getThrows() <= 1 && joe.getWinStrategy(1) == 0)
							{
								setForTwoDarts(joe, board);
								aim = true;
							}

							if (joe.getThrows() <= 2) //with one dart left, cycle through all one dart finishes and else score to get to a finish on the next round
							{
								for (int a = 0; a < 19; a++)
								{
									if (joe.getScore() == board.getDoubleValues(a)) //store one dart in array
									{
										joe.setWinStrategy(2, (board.getDoubleValues(a)));
										break;
									}
								}

								if (joe.getScore() == 50)
								{
									joe.setWinStrategy(2, 50);
									joe.setWinStrategy(0, 0);
									joe.setWinStrategy(1, 0);
								}
								aim = true;
							}

							//check if player can't get out with current score

							check = 0;

							for (int i = 0; i < 3; i++)
							{
								check = check + joe.getWinStrategy(i);
							}

							if (check == 0)
							{
								if (joe.getScore() > 61)
								{
									points = throwType.trebleThrow(joe.getSuccessRate(), 20);
									counter = joe.standardThrow(counter, points);
								}

								else
								{
									points = throwType.singleThrow(2);
									counter = joe.standardThrow(counter, points);
								}
								aim = false;
							}
						}

						if (joe.getWinStrategy(0) != 0 && aim == true) //is a three throw finish
						{
							//check score needed to determine type of throw
							points = determineTypeWithWinStrat(joe, throwType, 0);

							if (points != joe.getWinStrategy(0)) //if throw is missed
							{
								aim = false; //joe will reassess strategy
								for (int i = 0; i < 3; i++)
								{
									joe.setWinStrategy(i, 0);
								}
							}
							counter = joe.standardThrow(counter, points);
							joe.setWinStrategy(0, 0);
						}

						if (joe.getWinStrategy(1) != 0 && aim == true) //second throw of strategy/first of two throw finish
						{
							//check score needed to determine type of throw
							points = determineTypeWithWinStrat(joe, throwType, 1);

							if (points != joe.getWinStrategy(1))
							{
								aim = false;
								for (int i = 0; i < 3; i++)
								{
									joe.setWinStrategy(i, 0);
								}
							}

							counter = joe.standardThrow(counter, points);

							joe.setWinStrategy(1, 0);
						}

						if (joe.getWinStrategy(2) != 0 && aim == true) //last throw
						{
							points = throwType.doubleThrow(joe.getWinStrategy(2) / 2);
							if (points != joe.getWinStrategy(2))
							{
								aim = false;
								for (int i = 0; i < 3; i++)
								{
									joe.setWinStrategy(i, 0);
								}
							}
							counter = joe.standardThrow(counter, points);
							joe.setWinStrategy(2, 0);
						}
						checkBelowTwo(joe); //check if score has gone below two
					}

					if (joe.getScore() == 0) //joe has won game
					{
						game = 0;
						counter = 0;
						for (int i = 0; i < 3; i++) //reset saving throws array
						{
							joe.savingSet(i, 0);
						}
						joe.setGamesWon(joe.getGamesWon() + 1); //add one to games won
						canDoRisk = false;
						aim = false;
						selected = false;
						for (int i = 0; i < 3; i++) //reset saving throws array
						{
							joe.setWinStrategy(i, 0);
						}
					}

					if (joe.getThrows() == 3 && game == 1) //joe's turn is over, reset everything for next turn
					{
						joe.setThrows(0);
						counter = 0;
						for (int i = 0; i < 3; i++)
						{
							joe.savingSet(i, 0);
						}
						canDoRisk = false;
						aim = false;
						selected = false;
						for (int i = 0; i < 3; i++) //reset saving throws array
						{
							joe.setWinStrategy(i, 0);
						}
					}

					if (game == 0)
					{
						break; //stops sid from having another turn if joe has won the game
					}

					//Sid's turn

					while (sid.getScore() > 81 && sid.getThrows() < 3) //aim for 60
					{
						points = throwType.trebleThrow(sid.getSuccessRate(), 20);
						counter = sid.standardThrow(counter, points);
					}

					if (sid.getScore() == 81 && sid.getThrows() < 3) //aim for 57
					{
						points = throwType.trebleThrow(sid.getSuccessRate(), 19);
						counter = sid.standardThrow(counter, points);
					}

					if (sid.getScore() == 80 && sid.getThrows() < 3) //aim for 60
					{
						points = throwType.trebleThrow(sid.getSuccessRate(), 20);
						counter = sid.standardThrow(counter, points);
					}

					while (sid.getScore() < 80 && sid.getThrows() < 3 && sid.getScore() > 41)
					{
						if (sid.getScore() == 50)
						{
							points = throwType.bullThrow(sid.getSuccessRate());
							counter = sid.standardThrow(counter, points);
						}
						else //score 40
						{
							points = throwType.doubleThrow(20);
							counter = sid.standardThrow(counter, points);
						}
					}

					if (sid.getScore() == 41 && sid.getThrows() < 3) //aim for 1
					{
						points = throwType.singleThrow(1);
						counter = sid.standardThrow(counter, points);
					}

					while (sid.getScore() == 40 && sid.getThrows() < 3) //aim for 40, is a while loop as a double 20 could result in a score of 0
					{
						points = throwType.doubleThrow(20);
						counter = sid.standardThrow(counter, points);
					}

					//check for which double to aim for, score<40
					while (sid.getScore() < 40 && sid.getThrows() < 3 && sid.getScore() != 0)
					{
						typeOf = evenORodd(sid.getScore()); //if score is even, can aim for a double
						if (typeOf == "even")
						{
							points = throwType.doubleThrow(sid.getScore() / 2); //aim for double
						}

						else
						{
							points = throwType.singleThrow(1); //aiming for 1 to get score down to an equal number as to score a double next
						}

						counter = sid.standardThrow(counter, points);

						checkBelowTwo(sid); //check if score has gone below 2
					}

					if (sid.getScore() == 0) //sid has won game
					{
						game = 0;
						counter = 0;
						for (int i = 0; i < 3; i++) //reset saving throws array
						{
							sid.savingSet(i, 0);
						}
						sid.setGamesWon(sid.getGamesWon() + 1); //add one to games won
					}

					if (sid.getThrows() == 3 && game == 1) //sid's turn is over, reset everything for next turn
					{
						sid.setThrows(0);
						counter = 0;
						for (int i = 0; i < 3; i++)
						{
							sid.savingSet(i, 0);
						}
					}
				} //end of game loop
				if (game == 0) //swapping who goes first in next game of the set
				{
					if (result == 'J')
					{
						joe.setThrows(3);
						sid.setThrows(0);
						result = 'S';
					}
					else
					{
						result = 'J';
						joe.setThrows(0);
						sid.setThrows(0);
					}
				}
				game = 1; //reset game
				joe.setScore(501); //reset scores
				sid.setScore(501);

				//if a player has won the set, add a point to their set scores
				if (joe.getGamesWon() == 3)
				{
					joe.setSetsWon(joe.getSetsWon() + 1);
				}

				if (sid.getGamesWon() == 3)
				{
					sid.setSetsWon(sid.getSetsWon() + 1);
				}
			} //end of sets loop
			joe.setGamesWon(0);
			sid.setGamesWon(0); //reset for next set
		} //end of championship loop

		if (sid.getSetsWon() == 7)
		{
			//sid has won, check what joe scored
			possibleFrequency[0][joe.getSetsWon()] = 1 + possibleFrequency[0][joe.getSetsWon()];
		}

		else
		{
			//joe has won, check what sid scored
			place = sid.getSetsWon() + 7;
			possibleFrequency[0][place] = 1 + possibleFrequency[0][place];
		}

		sid.setSetsWon(0);
		joe.setSetsWon(0);
		setup = 0; //reset so for each match, sid goes first in the first game of the first set
	}

	cout << sid.getName() << "  " << joe.getName() << endl;
	for (int i = 0; i < 14; i++)
	{
		{
			cout << "  " << possibleFrequency[2][i] << " : " << possibleFrequency[1][i] << "       " << setprecision(4) << (float)possibleFrequency[0][i] / 100 << "%" << endl;
		}
	}
	system("pause");
}

string evenORodd(int a)
{
	if (a % 2 == 0) //if there's no remainder when dividing by 2
	{
		return "even";
	}
	else
	{
		return "odd";
	}
}

int determineTypeWithWinStrat(player& name, throwStrategy throwType, int i)
{
	int points = 0;
	//check score needed to determine type of throw
	if (name.getWinStrategy(i) < 21)
	{
		points = throwType.singleThrow(name.getWinStrategy(i));
	}

	else if (name.getWinStrategy(i) == 50)
	{
		points = throwType.bullThrow(name.getSuccessRate());
	}

	else if (name.getWinStrategy(i) == 25)
	{
		points = throwType.singleThrow(name.getWinStrategy(i));
	}

	else if (name.getWinStrategy(i) < 41)
	{
		points = throwType.doubleThrow(name.getWinStrategy(i) / 2);
	}

	else
	{
		points = throwType.trebleThrow(name.getSuccessRate(), name.getWinStrategy(i) / 3);
	}
	return points;
}

void checkBelowTwo(player& name)
{
	int minus = 0;
	if (name.getScore() < 2 && name.getScore() != 0) //checks if score is below 2
	{
		for (int a = 0; a < 3; a++)
		{
			minus = minus + name.getSet(a);
		}
		name.setScore(name.getScore() + minus); //resets score back to where it was at the beginning of the round
		name.setThrows(3);
	}
}

void setForThreeDarts(player& name, dartboard db)
{
	int b = 0;
	for (int a = 0; a < 103; a++)
	{
		if (name.getScore() == db.getThreeThrowFinish(0, a)) //check through all possible three dart finishes
		{
			b = 1;
			for (int i = 0; i < 3; i++) //store three darts required in an array
			{
				name.setWinStrategy(i, (db.getThreeThrowFinish(b, a)));
				b++;
			}
			return;
		}
	}
}

void setForTwoDarts(player& name, dartboard db) 
{
	int b = 0;
	for (int a = 0; a < 82; a++)
	{
		if (name.getScore() == db.getTwoThrowFinish(0, a)) //check through all possible two dart finishes
		{
			b = 1;
			for (int i = 1; i < 3; i++) //store two darts required in an array
			{
				name.setWinStrategy(i, (db.getTwoThrowFinish(b, a)));
				b++;
			}
			return;
		}
	}
}