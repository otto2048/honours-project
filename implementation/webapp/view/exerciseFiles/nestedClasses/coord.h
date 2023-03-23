
class coord {
    public:
        struct point
        {
            int value;

            point();
            point(int);
        };

        point points[2];

        coord();
        coord(int, int);
};