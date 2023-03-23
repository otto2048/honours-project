#include "Game.h"

Game::Game()
{
	//init deck of cards
	int counter = 0;

	for (int i = 0; i < 4; i++)
	{
		for (int j = 0; j < 13; j++)
		{
			cards[counter] = new Card(i, j + 1);
			counter++;
		}
	}
}

Game::~Game()
{
	for (int i = 0; i < numCards; i++)
	{
		if (cards[i])
		{
			delete cards[i];
		}
	}
}