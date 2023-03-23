#include &lt;string&gt;
#include &lt;cstdlib&gt; //random numbers header file
#include &lt;ctime&gt; //get date and time info

#include "coord.h"

class ship {
    private:
        const static int shipArrLen = 5;

    public:
        ship(std::string);

        coord shipPositions[shipArrLen];

        std::string shipName;

        void setRandomShipPositions();
};