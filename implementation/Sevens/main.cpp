#include "MySevensGame.h"

int main()
{
	srand(time(0)); //initialise random number generator with time

	MySevensGame game;

	game.initPlayerCards(0);
	game.initPlayerCards(1);

	return 0;
}