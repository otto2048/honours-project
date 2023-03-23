#pragma once
#include "Game.h"
class MySevensGame : public Game
{
public:
	void initPlayerCards(int);

private:
	bool checkIfCardHeld(Card*);
};

