#!/usr/bin/env python
# -*- coding: utf-8 -*-
# @Time    : 2018-11-02 20:44
# @Author  : Swilder_M
# @Site    : Poxiaobbs.com
# @File    : GetCatQT
# @Software: PyCharm

from PyQt5 import QtCore, QtGui, QtWidgets
from PyQt5.QtCore import *
from PyQt5.QtWidgets import *
import sys
import random

class BtnLabel(QtWidgets.QLabel):
    signal_time = QtCore.pyqtSignal(bool, int, int)

    def __init__(self, x, y, status, parent=None):
        super(BtnLabel, self).__init__(parent)

        self.image_en = QtGui.QPixmap()
        self.image_en.load(r"en.png")

        self.image_c = QtGui.QPixmap()
        self.image_c.load(r"cat2.png")

        self.image_ed = QtGui.QPixmap()
        self.image_ed.load(r"ed.png")

        self.x = x
        self.y = y
        self.status = status

    def mousePressEvent(self, e):  # 鼠标点击事件
        if self.status == 0:
            self.setPixmap(self.image_ed)
            self.status = 1
            self.signal_time.emit(True, self.x, self.y)
        else:
            self.signal_time.emit(False, self.x, self.y)


class Ui_MainWindow():
    def __init__(self):
        self.stepNum = 0
        self.move_met_o = {
            1: {'x': -1, 'y': -1},
            2: {'x': -1, 'y': 0},
            3: {'x': 0, 'y': 1},
            4: {'x': 1, 'y': 0},
            5: {'x': 1, 'y': -1},
            6: {'x': 0, 'y': -1},
        }
        self.move_met_j = {
            1: {'x': -1, 'y': 0},
            2: {'x': -1, 'y': 1},
            3: {'x': 0, 'y': 1},
            4: {'x': 1, 'y': 1},
            5: {'x': 1, 'y': 0},
            6: {'x': 0, 'y': -1},
        }

        self.click_status = {
            0: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            1: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            2: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            3: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            4: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            5: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            6: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            7: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}},
            8: {0: {}, 1: {}, 2: {}, 3: {}, 4: {}, 5: {}, 6: {}, 7: {}, 8: {}}
        }

        self.cat_x = random.randint(3, 6)
        self.cat_y = random.randint(3, 6)
        self.image_en = QtGui.QPixmap()
        self.image_en.load(r"en.png")

        self.image_c = QtGui.QPixmap()
        self.image_c.load(r"cat2.png")

        self.image_ed = QtGui.QPixmap()
        self.image_ed.load(r"ed.png")

    def setupUi(self, MainWindow):
        MainWindow.setFixedSize(480, 800)

        icon = QtGui.QIcon()
        icon.addPixmap(QtGui.QPixmap(r"cat2.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
        MainWindow.setWindowIcon(icon)

        MainWindow.move(760, 100)  # 移动部件位置   坐标为（300， 300）
        MainWindow.setWindowTitle('围住神经猫 By Ming QQ：1789162747')

        l1 = QtWidgets.QLabel(MainWindow)

        image = QtGui.QPixmap()
        image.load(r"bg.png")
        l1.setPixmap(image)
        l1.move(0, 0)

        stongList = []

        for i in range(81):
            x = i // 9
            y = i % 9
            t = random.randint(1, 100)
            if t % 11 == 0:
                status = 1  # 状态 0：空，可点击 1：不可点击 2：猫
                image1 = self.image_ed
            else:
                status = 0
                image1 = self.image_en

            if x == self.cat_x and y == self.cat_y:
                status = 2
                image1 = self.image_c
            self.click_status[x][y]['status'] = status

            l = BtnLabel(x, y, status, MainWindow)
            l.signal_time.connect(self.click_msg)  # 链接
            l.setPixmap(image1)
            if (x % 2 == 1):
                l.move(y * 48 + 10, x * 50 + 335)
            else:
                l.move(y * 48 + 34, x * 50 + 335)
            self.click_status[x][y]['label'] = l
            stongList.append(l)

    def retranslateUi(self, MainWindow):
        _translate = QtCore.QCoreApplication.translate
        MainWindow.setWindowTitle(_translate("MainWindow", "围住神经猫 By Ming QQ：1789162747"))

    def click_msg(self, cd_status, c_x, c_y):
        if cd_status:
            self.stepNum += 1
            self.click_status[c_x][c_y]['status'] = 1  # 状态 0：空，可点击 1：不可点击 2：猫
            self.cat_move()

    def cat_move(self):
        if (self.cat_x + 1) % 2 == 0:
            move_met = self.move_met_o
        else:
            move_met = self.move_met_j

        can_move = []

        if self.click_status[self.cat_x + move_met[1]['x']][self.cat_y + move_met[1]['y']]['status'] == 0:
            can_move.append(1)

        if self.click_status[self.cat_x + move_met[2]['x']][self.cat_y + move_met[2]['y']]['status'] == 0:
            can_move.append(2)

        if self.click_status[self.cat_x + move_met[3]['x']][self.cat_y + move_met[3]['y']]['status'] == 0:
            can_move.append(3)

        if self.click_status[self.cat_x + move_met[4]['x']][self.cat_y + move_met[4]['y']]['status'] == 0:
            can_move.append(4)

        if self.click_status[self.cat_x + move_met[5]['x']][self.cat_y + move_met[5]['y']]['status'] == 0:
            can_move.append(5)

        if self.click_status[self.cat_x + move_met[6]['x']][self.cat_y + move_met[6]['y']]['status'] == 0:
            can_move.append(6)

        if len(can_move) == 0:
            self.res_box(True, MainWindow)
        else:
            random.shuffle(can_move)

            self.click_status[self.cat_x][self.cat_y]['status'] = 0
            self.click_status[self.cat_x][self.cat_y]['label'].setPixmap(self.image_en)
            self.click_status[self.cat_x][self.cat_y]['label'].status = 0

            self.cat_x = self.cat_x + move_met[can_move[0]]['x']
            self.cat_y = self.cat_y + move_met[can_move[0]]['y']
            self.click_status[self.cat_x][self.cat_y]['status'] = 2
            self.click_status[self.cat_x][self.cat_y]['label'].setPixmap(self.image_c)
            self.click_status[self.cat_x][self.cat_y]['label'].status = 2

            if self.cat_x == 0 or self.cat_x == 8 or self.cat_y == 0 or self.cat_y == 8:
                self.res_box(False, MainWindow)

    def res_box(self, status, MainWindow):
        msgBox = QMessageBox()
        icon = QtGui.QIcon()
        icon.addPixmap(QtGui.QPixmap(r"cat2.png"), QtGui.QIcon.Normal, QtGui.QIcon.Off)
        msgBox.setWindowIcon(icon)

        msgBox.setWindowTitle('GAME OVER')
        msgBox.setFixedSize(100, 200)
        msgBox.move(900, 500)
        if status:
            pixmap = QtGui.QPixmap("s2.png")
            msgBox.setText("喔吼，你共走了" + str(self.stepNum) + '步就赢啦')
        else:
            pixmap = QtGui.QPixmap("s1.png")
            msgBox.setText("啦啦啦，你输了~")

        msgBox.setIconPixmap(pixmap)
        YesButton = msgBox.addButton(str("再来一次！"), msgBox.YesRole)
        NoButton = msgBox.addButton(str("不了不了~"), msgBox.NoRole)
        NoButton.clicked.connect(QCoreApplication.instance().quit)
        msgBox.exec()

        if msgBox.clickedButton() == YesButton:
            self.cat_x = random.randint(3, 6)
            self.cat_y = random.randint(3, 6)
            self.stepNum = 0
            for i in range(9):
                for j in range(9):
                    t = random.randint(1, 100)
                    if t % 11 == 0:
                        # 状态 0：空，可点击 1：不可点击 2：猫
                        self.click_status[i][j]['label'].setPixmap(self.image_ed)
                        self.click_status[i][j]['label'].status = 1
                        self.click_status[i][j]['status'] = 1
                    else:
                        self.click_status[i][j]['label'].setPixmap(self.image_en)
                        self.click_status[i][j]['label'].status = 0
                        self.click_status[i][j]['status'] = 0

                    if i == self.cat_x and j == self.cat_y:
                        self.click_status[i][j]['label'].setPixmap(self.image_c)
                        self.click_status[i][j]['label'].status = 2
                        self.click_status[i][j]['status'] = 2


if __name__ == '__main__':
    app = QApplication(sys.argv)
    MainWindow = QWidget()
    ui = Ui_MainWindow()
    ui.setupUi(MainWindow)
    MainWindow.show()
    sys.exit(app.exec_())
