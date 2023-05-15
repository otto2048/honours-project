#include "ship.h"
#include &lt;cstdlib&gt; //random numbers header file
#include &lt;ctime&gt;   //get date and time info
#include &lt;iostream&gt;
#include &lt;string&gt;

using namespace std;

int main() {
	srand(time(0)); // initialise random number generator with time

	int arr[5];

	int a = 5;
	int *pointerTest = &a;

	ship ship("Titanic");

	ship.setRandomShipPositions();

	ship.shipName = "test";
	ship.shipName = "test2";
	ship.shipName = "test3";
	ship.shipName = "test4";

	cout << "ship name: " << ship.shipName << endl;

	return 0;
}