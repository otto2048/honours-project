#include "gtest/gtest.h"
#include <iostream>

int main(int argc, char **argv) {
  ::testing::InitGoogleTest(&argc, argv);

  const int rv = RUN_ALL_TESTS();

  std::cout << "DEBUGGING_TOOL_RESULT:" << std::endl;
  std::cout << ::testing::UnitTest::GetInstance()->successful_test_count() << std::endl;

  return rv;
}