#pragma once
#include "Card.h"
#include <algorithm>

class Player
{
public:
	const static int numCards = 7;

	Player();
	Player(int);

	Card getCard(int);

	void setCard(Card, int);

	int getWorstCard();
	void swapCards(Card&, int);

protected:
	Card cards[numCards]{};
	int score;
	int id;
};

