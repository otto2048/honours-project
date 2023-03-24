#include "MySevensGame.h"
#include <iostream>

using std::cout;
using std::endl;

int main()
{
	srand(time(0)); //initialise random number generator with time

	MySevensGame game;

	game.shuffleCards();

	game.initPlayerCards();

	game.getVisibleCard();

	for (int i = 0; i < 500; i++)
	{
		bool takesHidden = rand() > (RAND_MAX / 2);
		if (i % 2 == 0)
		{
			game.playTurn(takesHidden, 100, 0);
		}
		else
		{
			game.playTurn(takesHidden, 100, 1);
		}

		cout << game.getSwitchPoint() << endl;
	}

	return 0;
}