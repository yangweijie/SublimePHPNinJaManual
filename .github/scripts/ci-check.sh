#!/usr/bin/env bash
#
# 本地与 CI 共用的校验脚本：语法检查 + 内置 ThinkPHP 冒烟测试。
#
#   bash .github/scripts/ci-check.sh
#
# 环境变量：
#   PHP_BIN     php 可执行文件（默认 php）
#   PYTHON_BIN  python 可执行文件（默认 python3）
#
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
cd "$ROOT"

PHP_BIN="${PHP_BIN:-php}"
PYTHON_BIN="${PYTHON_BIN:-python3}"

echo "==> 1/3 Python 语法检查"
"$PYTHON_BIN" -m py_compile ./*.py
echo "    OK"
# py_compile 会生成 __pycache__，而本仓库没有忽略它，顺手清掉
rm -rf __pycache__

echo "==> 2/3 PHP 语法检查"
count=0
# tp5/runtime 是框架运行时缓存（含冒烟测试编译出来的模板），不是源码，跳过
while IFS= read -r -d '' f; do
	if ! "$PHP_BIN" -l "$f" >/dev/null 2>&1; then
		echo "    语法错误：$f"
		"$PHP_BIN" -l "$f"
		exit 1
	fi
	count=$((count + 1))
done < <(find tp5 -name '*.php' -not -path 'tp5/runtime/*' -print0)
echo "    OK（$count 个文件）"

echo "==> 3/3 冒烟测试：实际跑一次内置 ThinkPHP"
# URL 必须整体加引号，否则 POSIX shell 会把 app\sublime\... 里的反斜杠当转义符吃掉
args="$("$PHP_BIN" -r 'echo base64_encode(json_encode(["function" => "array_map", "lang" => "zh"]));')"
out="$("$PHP_BIN" tp5/public/index.php "sublime/index/run/call/app\\sublime\\command\\FindCommentCmd/args/$args")"

printf '%s' "$out" | "$PHP_BIN" -r '
$raw = trim(stream_get_contents(STDIN));
$d   = json_decode(base64_decode($raw), true);

if (!is_array($d) || !isset($d["code"]) || 200 !== $d["code"]) {
	fwrite(STDERR, "    冒烟测试失败，PHP 原始输出：\n" . substr($raw, 0, 800) . "\n");
	exit(1);
}

echo "    OK（code=" . $d["code"] . ", type=" . ($d["type"] ?? "") . "）\n";
'

echo "==> 全部通过"
