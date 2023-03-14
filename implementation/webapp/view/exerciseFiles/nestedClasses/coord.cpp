#include "coord.h"

coord::point::point() {
    value = 0;
}

coord::coord() {
    points[0] = point();
    points[1]= point();
}