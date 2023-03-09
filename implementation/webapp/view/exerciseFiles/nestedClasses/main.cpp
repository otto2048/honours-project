#include &lt;iostream&gt;
#include &lt;string&gt;
#include "ship.h"

using namespace std;

int main()
{
    ship ship("Titanic");

    cout << "ship name: " << ship.shipName << endl;

    return 0;
}