#include &lt;iostream&gt;
#include &lt;string&gt;
#include "name.h"

using namespace std;

void hi(string name)
{
    cout<<"hi " << name <<endl;
}

string myName()
{
    return "otto";
}

int main()
{
    hi(myName());

    name name;

    name.hi(name.myName());

    return 0;
}