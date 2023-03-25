#pragma once
#include <cstdlib> // random numbers header file//
#include <ctime> // used to get date and time information

#include "Player.h"

class Game
{
public:

	//number of cards in full deck
	const static int numCards = 52;

	//number of players
	const static int numPlayers = 2;

	Game();

	int getSwitchPoint();

protected:

	//deck of cards
	Card fullDeck[numCards];

	//collection of players
	Player players[numPlayers] = { Player(0), Player(1) };

};

