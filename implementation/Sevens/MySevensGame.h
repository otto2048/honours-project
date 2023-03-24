#pragma once
#include "Game.h"
#include <algorithm>
class MySevensGame : public Game
{
public:
	void initPlayerCards();
	void shuffleCards();
	int getVisibleCard();
	void playTurn(bool, int, int);

private:
	bool checkIfCardHeld(Card);
};

