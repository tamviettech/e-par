<?php
/**


This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
?>
<?php
defined('SERVER_ROOT') or die();
$this->template->title = 'Kiểm tra trước hồ sơ';
$this->template->display('dsp_header_pop_win.php');
?>
<h4>Kiểm tra trước hồ sơ là gì?</h4>
<p>
    Kiểm tra trước hồ sơ là chức năng mở rộng của dịch vụ công trực tuyến:<br/>
<ul>
    <li>Công dân có thể kiểm tra xem Hồ sơ đúng, đủ chưa trước khi nộp thật.</li>
    <li>Hồ sơ được nộp thử qua mạng sẽ được Bộ phận một cửa tiếp nhận và kiểm tra về hình thức.</li>
    <li>Tiếp đó Hồ sơ được chuyển qua Phòng chuyên môn kiểm tra về nội dung (nếu cần thiết).</li>
    <li>Bộ phận một cửa thông báo kết quả cho Công dân qua điện thoại, thư điện tử (nếu phải bổ sung hoặc chỉnh sửa sẽ thông báo rõ).</li>
</ul>
</p>
<?php
$this->template->display('dsp_footer_pop_win.php');