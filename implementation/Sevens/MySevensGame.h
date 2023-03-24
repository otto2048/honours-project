#pragma once
#include "Game.h"
#include <algorithm>
#include <iostream>

using std::cout;
using std::endl;

class MySevensGame : public Game
{
public:
	void shuffleCards();

	void playTurn(bool, int, int);

	void initCards();

	bool numberEqualityWinCondition(Card[], int);
	bool sequenceEqualityWinCondition(Card[], int);

	void bubbleSort(Card[], int);
};

