#pragma once
#include "Card.h"

class Player
{
public:
	const static int numCards = 7;

	Player();

protected:
	Card* cards[numCards];
	int score;
};

