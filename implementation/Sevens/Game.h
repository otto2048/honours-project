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

	//number of cards in full stack once player hands are given out
	const static int numStackCards = numCards - 15;

	Game();
	~Game();


	int getSwitchPoint();
protected:

	//deck of cards
	Card fullDeck[numCards];

	//stack of cards in play
	Card stack[numStackCards];

	//the wildcard for this round
	int wildcard;

	//the point the stack of cards in split at
	int switchPoint;

	//collection of players
	Player players[numPlayers] = { Player(0), Player(1) };

};

