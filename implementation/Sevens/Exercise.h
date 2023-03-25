#pragma once
#include "Game.h"
#include <algorithm>
#include <iostream>

using std::cout;
using std::endl;

class Exercise : Game
{
public:
	void shuffleCards();

	void initGameCards();

	Card* pickHiddenCard(int);

	Card* pickVisibleCard(int);

	void switchCard(Card*, int);

	bool numberEqualityWinCondition(Card[]);

	bool sequenceEqualityWinCondition(Card[]);

private:
	void swapCards(Card&, Card&);
	void bubbleSort(Card[], int);
};

