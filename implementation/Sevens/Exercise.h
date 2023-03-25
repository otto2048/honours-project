#pragma once
#include "Game.h"
#include <algorithm>
#include <iostream>

using std::cout;
using std::endl;

class Exercise : Game
{
public:
	//number of cards in full stack once player hands are given out
	const static int numStackCards = numCards - 15;

	void shuffleCards();

	void initGameCards();

	Card* pickHiddenCard(int);

	Card* pickVisibleCard(int);

	void switchCard(Card*, int);

	bool numberEqualityWinCondition(Card[]);

	bool sequenceEqualityWinCondition(Card[]);

private:
	//stack of cards in play
	Card stack[numStackCards];

	//the wildcard for this round
	int wildcard;

	//the point the stack of cards is split at
	int switchPoint;

	void swapCards(Card&, Card&);
	void bubbleSort(Card[], int);
};

