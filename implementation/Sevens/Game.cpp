#include "Game.h"

Game::Game()
{
	//init deck of cards
	int counter = 0;

	for (int i = 0; i < 4; i++)
	{
		for (int j = 0; j < 13; j++)
		{
			fullDeck[counter] = Card(i, j + 1);
			counter++;
		}
	}

	switchPoint = 0;
}

int Game::getSwitchPoint()
{
	return switchPoint;
}