import os
import hashlib

def get_files_with_hashes(root_dir, exclude_dirs):
    file_hashes = {}
    for dirpath, dirnames, filenames in os.walk(root_dir):
        dirnames[:] = [d for d in dirnames if d not in exclude_dirs]
        for filename in filenames:
            filepath = os.path.join(dirpath, filename)
            rel_path = os.path.relpath(filepath, root_dir)
            try:
                with open(filepath, 'rb') as f:
                    file_hashes[rel_path] = hashlib.md5(f.read()).hexdigest()
            except Exception:
                pass
    return file_hashes

older_dir = r'D:\InvestAssist\InvestAssist'
newer_dir = r'D:\InvestAssist\InvestAssist_v10'
exclude = ['vendor', 'node_modules', '.git', 'storage', 'bootstrap', 'public']

older_hashes = get_files_with_hashes(older_dir, exclude)
newer_hashes = get_files_with_hashes(newer_dir, exclude)

older_set = set(older_hashes.keys())
newer_set = set(newer_hashes.keys())

only_in_older = older_set - newer_set
only_in_newer = newer_set - older_set
common_files = older_set.intersection(newer_set)

different_content = [f for f in common_files if older_hashes[f] != newer_hashes[f]]

total = len(older_hashes)
matched = len(common_files) - len(different_content)
percentage = (matched / total) * 100 if total > 0 else 100

print(f'\n=== KET QUA DONG BO ===')
print(f'Tong so file goc: {total}')
print(f'So file giong nhau 100%: {matched}')
print(f'Ty le dong bo: {percentage:.2f}%')

if only_in_older:
    print(f'\n--- Thieu {len(only_in_older)} file ben v10 ---')
    for f in list(only_in_older)[:20]:
        print(f)
    if len(only_in_older) > 20: print('...')

if different_content:
    print(f'\n--- Co {len(different_content)} file khac noi dung ---')
    for f in list(different_content)[:20]:
        print(f)
    if len(different_content) > 20: print('...')
