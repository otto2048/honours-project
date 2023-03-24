#pragma once
#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

#include "Player.h"

class Game
{
public:

	const static int numCards = 52;
	const static int numPlayers = 2;
	const static int numStackCards = numCards - 15;

	Game();
	~Game();
	Player players[numPlayers] = { Player(0), Player(1) };

	int getSwitchPoint();
protected:
	Card fullDeck[numCards];
	Card stack[numStackCards];

	Card* chosenCard;

	int wildcard;

	int switchPoint;


};

