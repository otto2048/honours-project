#include &lt;iostream&gt;
#include &lt;string&gt;
#include "ship.h"

using namespace std;

int main()
{
    int arr[5];
    
    ship ship("Titanic");

    cout << "ship name: " << ship.shipName << endl;

    return 0;
}