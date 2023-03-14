#include &lt;iostream&gt;
#include &lt;string&gt;
#include "ship.h"

using namespace std;

int main()
{
    int arr[5];
    
    ship ship("Titanic");

    
  	ship.shipName = "test";
  ship.shipName = "test2";
  ship.shipName = "test3";
  ship.shipName = "test4";


    cout << "ship name: " << ship.shipName << endl;

    return 0;
}