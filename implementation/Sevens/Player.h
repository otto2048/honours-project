#pragma once
#include "Card.h"
#include <algorithm>

class Player
{
public:
	const static int numCards = 7;
	const static int fourCards = 4;
	const static int threeCards = 3;

	Player();
	Player(int);

	Card getCard(int);

	void setCard(Card, int);

	int getWorstCard();

protected:
	Card cards[numCards]{};

	Card groupFourCards[fourCards]{};

	Card groupThreeCards[threeCards]{};

	int score;
	int id;
};

