#include "MySevensGame.h"
#include <iostream>

using std::cout;
using std::endl;

int main()
{
	srand(time(0)); //initialise random number generator with time

	MySevensGame game;

	game.shuffleCards();

	game.initCards();

	for (int i = 0; i < 10; i++)
	{
		bool takesHidden = rand() > (RAND_MAX / 2);
		if (i % 2 == 0)
		{
			game.playTurn(false, 100, 0);
		}
		else
		{
			game.playTurn(false, 100, 1);
		}

		cout << takesHidden << " " << game.getSwitchPoint() << endl;
	}

	Card cards[4] = { Card(1, 1), Card(0, 4), Card(0, 2), Card(0,3) };

	game.numberEqualityWinCondition(cards, 4);
	game.sequenceEqualityWinCondition(cards, 4);
	
	return 0;
}