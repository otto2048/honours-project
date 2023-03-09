#include &lt;string&gt;

#include "coord.h"

class ship {

    public:
        ship(std::string);

        coord shipCoords;
        std::string shipName;
};