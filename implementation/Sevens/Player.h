#pragma once
#include "Card.h"
#include <algorithm>

class Player
{
public:
	//number of cards in a players hand at any time
	const static int numCards = 7;

	Player();
	Player(int);

	//getter and setter for cards in our hand
	Card getCard(int);
	void setCard(Card, int);

	//get position in our cards of the card we want to replace
	int getReplacementCard(Card&);
	int getReplacementCardBugs(Card&);

protected:

	//players hand of cards
	Card cards[numCards]{};

	int score;
	int id;
};

