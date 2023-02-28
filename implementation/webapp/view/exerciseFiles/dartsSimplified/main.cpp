#include &lt;iostream&gt;
#include &lt;string&gt;
#include "player.h"
#include "throwStrategy.h"
#include &lt;cstdlib&gt; //random numbers header file
#include &lt;ctime&gt; //get date and time info
#include &lt;iomanip&gt; //for precision
using namespace std;

int main()
{
	joepoints = 0;
	sidpoints = 0;

	srand(time(0)); //initialise random number generator with time

	//create objects
	player joe("Joe", 71, 501);
	joe.setThrows(0);
	joe.setSetsWon(0);
	joe.setGamesWon(0);

	player sid("Sid", 73, 501);
	sid.setThrows(0);
	sid.setSetsWon(0);
	sid.setGamesWon(0);

	throwStrategy throwType;

	for (int a = 0; a < 20; a++) //initialising double values of the dartboard
	{
		joepoints = throwType.bullThrow(joe.getSuccessRate());
		sidpoints = throwType.bullThrow(sid.getSuccessRate());
	}
}