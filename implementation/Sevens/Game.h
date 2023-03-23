#pragma once
#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

#include "Player.h"

class Game
{
public:
	const static int numCards = 52;
	const static int numPlayers = 2;

	Game();
	~Game();
protected:
	Card* cards[numCards];

	Card* chosenCard;

	int wildcard;

	Player players[numPlayers] = {Player(0), Player(1)};

};

