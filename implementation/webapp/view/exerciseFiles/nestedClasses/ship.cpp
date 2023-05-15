#include "ship.h"

ship::ship(std::string n)
{
    shipName = n;
}

void ship::setRandomShipPositions()
{
    for (int i = 0; i < shipArrLen; i++)
    {
        int x = rand() % 100;
        int y = rand() % 100;

        shipPositions[i] = coord(x, y);
    }
}