#include "coord.h"

coord::point::point() {
    value = 0;
}

coord::point::point(int val) {
    value = val;
}

coord::coord() {
    points[0] = point();
    points[1]= point();
}

coord::coord(int x, int y) {
    points[0] = point(x);
    points[1]= point(y);
}