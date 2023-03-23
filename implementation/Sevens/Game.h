#pragma once
#include "Player.h"

class Game
{
public:
	const static int numCards = 52;

	Game();
	~Game();
private:
	Card* cards[numCards];

	int wildcard;
	
	Player playerOne;
	Player playerTwo;
};

