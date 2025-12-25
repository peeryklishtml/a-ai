import os

# Configuration
root_dir = "."
output_file = "conteudo_completo.txt"
exclude_dirs = ["assets", ".git", ".gemini"]
exclude_files = ["generate_full_content.py", output_file]

def is_text_file(filepath):
    # Simple check for text files based on extension or content could be added here
    # For now, we assume standard web files are text
    return True

with open(output_file, "w", encoding="utf-8") as outfile:
    for dirpath, dirnames, filenames in os.walk(root_dir):
        # Filter directories
        dirnames[:] = [d for d in dirnames if d not in exclude_dirs]
        
        for filename in filenames:
            if filename in exclude_files:
                continue
                
            filepath = os.path.join(dirpath, filename)
            
            # Skip the output file itself if it exists (though usually handled by exclude_files)
            if os.path.abspath(filepath) == os.path.abspath(output_file):
                continue

            try:
                with open(filepath, "r", encoding="utf-8", errors='ignore') as infile:
                    content = infile.read()
                    outfile.write(f"\n{'='*50}\n")
                    outfile.write(f"FILE: {filepath}\n")
                    outfile.write(f"{'='*50}\n\n")
                    outfile.write(content)
                    outfile.write("\n")
                print(f"Processed: {filepath}")
            except Exception as e:
                print(f"Skipping {filepath}: {e}")

print(f"Done! Content written to {output_file}")
