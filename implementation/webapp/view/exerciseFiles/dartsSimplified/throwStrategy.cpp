#include "throwStrategy.h"
#include "dartboard.h"
#include &lt;cstdlib&gt;
#include &lt;ctime&gt;
using namespace std;

dartboard board;

int throwStrategy::bullThrow(int s)
{
	int random = rand() % 100; 
	if (random < (s - 20)) 
	{
		return 50;
	}
	else if (random < s) 
	{
		return 25; 
	}
	else 
	{
		return 1 + rand() % 20;
	}
}

int throwStrategy::singleThrow(int aim)
{
	int random = rand() % 100;

	if (aim == 25)
	{
		if (random < 80)
		{
			return 25;
		}
		else if (random < 90)
		{
			return 50;
		}
		else
		{
			return 1 + rand() % 20;
		}
	}

	if (random < 88)
	{
		return aim;
	}

	else if (random < 92)
	{
		return board.getBoardValue(0, aim);
	}

	else if (random < 96)
	{
		return board.getBoardValue(1, aim);
	}

	else if (random < 98)
	{
		return 3 * aim;
	}

	else
	{
		return 2 * aim;
	}
}

int throwStrategy::doubleThrow(int aim)
{
	int random = rand() % 100;

	if (random < 80)
	{
		return 2 * aim;
	}

	else if (random < 85)
	{
		return 0;
	}

	else if (random < 90)
	{
		return aim;
	}

	else if (random < 93)
	{
		return 2 * (board.getBoardValue(0, aim));
	}

	else if (random < 96)
	{
		return 2 * (board.getBoardValue(1, aim));
	}

	else if (random < 98)
	{
		return board.getBoardValue(0, aim);
	}

	else
	{
		return board.getBoardValue(1, aim);
	}
}

int throwStrategy::trebleThrow(int s, int aim) 
{
	int r = rand() % 100;

	if (r < s)
	{
		return 3 * aim;
	}
		
	else if (r < 90)
	{
		return aim;
	}

	else if (r < 93)
	{
		return 3 * (board.getBoardValue(0, aim));
	}
		
	else if (r < 96)
	{
		return 3 * (board.getBoardValue(1, aim));
	}

	else if (r < 98)
	{
		return board.getBoardValue(0, aim);
	}

	else
	{
		return board.getBoardValue(1, aim);
	}
}