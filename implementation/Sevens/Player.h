#pragma once
#include "Card.h"

class Player
{
public:
	const static int numCards = 7;

	Player();
	Player(int);

	Card* getCard(int);

	void setCard(Card*, int);

protected:
	Card* cards[numCards]{};
	int score;
	int id;
};

