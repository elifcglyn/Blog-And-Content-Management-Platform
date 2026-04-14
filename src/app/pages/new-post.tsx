import * as React from "react";
import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
import Image from '@tiptap/extension-image' 
import { common, createLowlight } from 'lowlight'
import { 
  Bold, Italic, Heading1, Heading2, Quote, 
  Terminal, Save, Sparkles, History, Eye, X,
  ImagePlus, UploadCloud, ImageIcon, Trash2, 
  Tag // 🚀 YENİ: Kategori ikonu
} from 'lucide-react'
import { Button } from "../components/ui/button";

const lowlight = createLowlight(common)

// 📌 🚀 YENİ: Seçilebilecek Kategoriler Listesi
const CATEGORIES = [
  { id: "yazilim", label: "Yazılım" },
  { id: "teknoloji", label: "Teknoloji" },
  { id: "bilim", label: "Bilim" },
  { id: "finans", label: "Finans & Ekonomi" },
  { id: "saglik", label: "Sağlık & Yaşam" },
  { id: "spor", label: "Spor" },
  { id: "yemek", label: "Gastronomi" },
  { id: "sanat", label: "Sanat & Tasarım" },
  { id: "moda", label: "Moda" }
];

export function NewPostPage() {
  const [previewOpen, setPreviewOpen] = React.useState(false); 
  
  const [coverImage, setCoverImage] = React.useState<string | null>(null);
  const [title, setTitle] = React.useState("");
  
  // 🚀 Kategori State'i
  const [category, setCategory] = React.useState("yazilim");
  const [versionNote, setVersionNote] = React.useState(""); 

  const editor = useEditor({
    extensions: [
      StarterKit,
      CodeBlockLowlight.configure({ lowlight }),
      Image.configure({
        HTMLAttributes: {
          class: 'rounded-2xl border border-slate-100 shadow-sm max-w-full h-auto my-8',
        },
      }),
    ],
    content: `
      <h2>Yeni bir hikaye başlasın...</h2>
      <p>Aşağıdaki "Resim Ekle" butonuna basarak yazı içine görsel koyabilirsin.</p>
    `,
    editorProps: {
      attributes: {
        class: 'prose prose-lg max-w-none focus:outline-none min-h-[500px] font-sans p-8 px-12 selection:bg-teal-100 dark:prose-invert',
      },
    },
  });

  // 🚀 GERÇEK BACKEND BAĞLANTISI: Yayınla Butonuna Basıldığında...
  const handlePublish = () => {
    if (!editor) return;
    
    // Metin olan kategorileri veritabanı ID'sine çeviren sözlük
    const kategoriCevirici: any = {
      "yazilim": 1,
      "teknoloji": 2,
      "bilim": 3,
      "finans": 4,
      "saglik": 5,
      "spor": 6,
      "yemek": 7,
      "sanat": 8,
      "moda": 9
    };

    const gercekKategoriId = kategoriCevirici[category] || 1;

    // Veritabanındaki sütun isimlerimize uyumlu paket
    const veriPaketi = {
      yazar_id: 1, 
      kategori_id: gercekKategoriId, 
      baslik: title,
      ozet: editor.getText().substring(0, 150) + "...", 
      icerik: editor.getHTML(),
      kapak_resmi: coverImage || null 
    };

    // PHP Servisimize POST isteği
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/yazi_ekle.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(veriPaketi),
    })
    .then(response => response.json())
    .then(data => {
      if(data.hata) {
        alert("Hata oluştu: " + data.hata);
      } else {
        alert("Harika! Yazın başarıyla veritabanına eklendi.");
        setTitle("");
        setCoverImage(null);
        editor.commands.setContent("<h2>Yeni bir hikaye başlasın...</h2>");
      }
    })
    .catch(error => {
      console.error("Bağlantı hatası:", error);
      alert("Sunucuya bağlanılamadı!");
    });
  };

  if (!editor) return null;
  const editorHtml = editor.getHTML();

  const toBase64 = (file: File): Promise<string> => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result as string);
    reader.onerror = reject;
  });

  const coverInputRef = React.useRef<HTMLInputElement>(null);
  const handleCoverImageChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      const base64 = await toBase64(file);
      setCoverImage(base64); 
    }
  };

  const inlineImageInputRef = React.useRef<HTMLInputElement>(null);
  const handleInlineImageUpload = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file && editor) {
      const base64 = await toBase64(file);
      editor.chain().focus().setImage({ src: base64 }).run();
    }
    if (event.target) event.target.value = '';
  };

  return (
    <div className="flex-1 flex flex-col bg-white font-sans dark:bg-background">
      
      <style dangerouslySetInnerHTML={{__html: `
        .ProseMirror pre { background-color: #f8fafc !important; color: #334155 !important; padding: 1.5rem !important; border-radius: 1rem !important; border: 1px solid #f1f5f9 !important; margin: 1.5rem 0 !important; overflow-x: auto; }
        .ProseMirror code { background-color: #f8fafc !important; color: #0f766e !important; padding: 0.25rem 0.5rem !important; border-radius: 0.5rem !important; font-weight: 600 !important; }
        .ProseMirror pre code { background-color: transparent !important; padding: 0 !important; }
        .prose code::before, .prose code::after { content: none !important; }
        .ProseMirror img.ProseMirror-selectednode { outline: 3px solid #0f766e; box-shadow: 0 0 0 6px rgba(15, 118, 110, 0.2); }
      `}} />

      <input type="file" accept="image/*" ref={coverInputRef} onChange={handleCoverImageChange} className="hidden" />
      <input type="file" accept="image/*" ref={inlineImageInputRef} onChange={handleInlineImageUpload} className="hidden" />

      <main className="container mx-auto max-w-5xl py-12 px-6">
        
        <div className="flex justify-between items-end mb-10 pb-6 border-b border-slate-100 dark:border-slate-800">
          <div className="space-y-2">
            <div className="flex items-center gap-2 text-teal-600 font-bold text-xs uppercase tracking-widest">
              <Sparkles className="w-4 h-4" /> <span>Postify Editor v2.5 (Görsel Destekli)</span>
            </div>
            <h1 className="font-serif text-5xl italic tracking-tighter text-slate-900 dark:text-foreground">Yeni Hikaye Yarat</h1>
          </div>
          <div className="flex items-center gap-2">
            <Button 
              variant="outline" 
              onClick={() => setPreviewOpen(true)}
              className="rounded-full px-6 border-slate-200 dark:border-border gap-2 hover:bg-teal-50 hover:text-teal-700"
            >
              <Eye className="w-4 h-4" /> Ön İzleme
            </Button>
            {/* 🚀 YENİ: Yayınla Butonuna onClick eklendi */}
            <Button 
              onClick={handlePublish}
              disabled={!title} // Başlık yoksa buton pasif olur
              className="bg-slate-900 text-white rounded-full px-8 hover:bg-teal-600 transition-all border-none disabled:opacity-50"
            >
              <Save className="w-4 h-4 mr-2" /> Yayınla
            </Button>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          
          <div className="lg:col-span-3 space-y-8">
            <div className="w-full aspect-[21/9] bg-slate-50 dark:bg-slate-900 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-teal-200 transition-all overflow-hidden group relative flex items-center justify-center">
              {coverImage ? (
                <>
                  <img src={coverImage} alt="Kapak Fotoğrafı" className="w-full h-full object-cover" />
                  <div className="absolute inset-0 bg-slate-900/60 flex items-center justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm">
                    <Button onClick={() => coverInputRef.current?.click()} className="bg-white text-slate-900 hover:bg-teal-50 rounded-full">
                      <ImageIcon className="w-4 h-4 mr-2" /> Değiştir
                    </Button>
                    <Button onClick={() => setCoverImage(null)} variant="destructive" className="rounded-full bg-red-600">
                      <Trash2 className="w-4 h-4 mr-2" /> Sil
                    </Button>
                  </div>
                </>
              ) : (
                <button onClick={() => coverInputRef.current?.click()} className="text-center p-12 flex flex-col items-center gap-4 text-slate-500 hover:text-teal-600">
                  <UploadCloud className="w-12 h-12 text-slate-300 group-hover:text-teal-400 transition-colors" />
                  <div className="space-y-1">
                    <p className="font-semibold text-slate-700 dark:text-slate-300">Yazı Kapak Fotoğrafı Ekle</p>
                    <p className="text-xs text-slate-400">Önerilen boyut: 1600x900px (Maksimum 5MB simülasyon)</p>
                  </div>
                </button>
              )}
            </div>

            <input 
              type="text"
              placeholder="Hikayenin Başlığı..."
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full font-serif text-6xl italic border-none focus:ring-0 outline-none p-0 text-slate-900 dark:text-foreground placeholder:text-slate-200 bg-transparent"
            />

            <div className="border border-slate-100 dark:border-border rounded-[2.5rem] shadow-sm bg-white dark:bg-card overflow-hidden relative">
              <div className="flex flex-wrap items-center gap-1 p-3 border-b border-slate-50 dark:border-border bg-slate-50/50 dark:bg-muted/50 sticky top-0 z-10 backdrop-blur-sm">
                <button type="button" onClick={() => inlineImageInputRef.current?.click()} className="flex items-center gap-2 px-3 py-1.5 rounded-xl transition-all font-bold text-xs text-slate-600 hover:bg-slate-900 hover:text-white"><ImagePlus className="w-4 h-4" /> RESİM EKLE</button>
                <div className="w-px h-4 bg-slate-200 dark:bg-border mx-2" />
                <button type="button" onClick={() => editor.chain().focus().toggleBold().run()} className={`p-2 rounded-xl transition-colors ${editor.isActive('bold') ? 'bg-teal-100 text-teal-700' : 'text-slate-600 hover:bg-teal-50'}`}><Bold className="w-5 h-5" /></button>
                <button type="button" onClick={() => editor.chain().focus().toggleItalic().run()} className={`p-2 rounded-xl transition-colors ${editor.isActive('italic') ? 'bg-teal-100 text-teal-700' : 'text-slate-600 hover:bg-teal-50'}`}><Italic className="w-5 h-5" /></button>
                <div className="w-px h-4 bg-slate-200 dark:bg-border mx-2" />
                <button type="button" onClick={() => editor.chain().focus().toggleHeading({ level: 1 }).run()} className={`p-2 rounded-xl ${editor.isActive('heading', { level: 1 }) ? 'bg-teal-100 text-teal-700' : 'text-slate-600 hover:bg-teal-50'}`}><Heading1 className="w-5 h-5" /></button>
                <button type="button" onClick={() => editor.chain().focus().toggleHeading({ level: 2 }).run()} className={`p-2 rounded-xl ${editor.isActive('heading', { level: 2 }) ? 'bg-teal-100 text-teal-700' : 'text-slate-600 hover:bg-teal-50'}`}><Heading2 className="w-5 h-5" /></button>
                <div className="w-px h-4 bg-slate-200 dark:bg-border mx-2" />
                <button type="button" onClick={() => editor.chain().focus().toggleCodeBlock().run()} className={`flex items-center gap-2 px-3 py-1.5 rounded-xl transition-all font-bold text-xs ${editor.isActive('codeBlock') ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-900 hover:text-white'}`}><Terminal className="w-4 h-4" /> KOD BLOĞU EKLE</button>
                <button type="button" onClick={() => editor.chain().focus().toggleBlockquote().run()} className={`p-2 rounded-xl ${editor.isActive('blockquote') ? 'bg-teal-100 text-teal-700' : 'text-slate-600 hover:bg-teal-50'}`}><Quote className="w-5 h-5" /></button>
              </div>
              <EditorContent editor={editor} />
            </div>
          </div>

          {/* 🛠️ SAĞ PANEL (KATEGORİ VE SÜRÜM NOTU) */}
          <aside className="lg:col-span-1 space-y-6">
             
             {/* 🚀 YENİ: KATEGORİ SEÇİM KUTUSU */}
             <div className="p-6 bg-white dark:bg-slate-900 rounded-[2rem] border border-slate-100 dark:border-slate-800 shadow-sm">
                <h3 className="font-serif text-xl italic mb-4 flex items-center gap-2 text-slate-900 dark:text-white">
                  <Tag className="w-5 h-5 text-teal-600" /> Kategori
                </h3>
                <div className="relative group">
                  <select
                    value={category}
                    onChange={(e) => setCategory(e.target.value)}
                    className="w-full appearance-none bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl px-5 py-4 text-sm font-bold text-slate-700 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-teal-500/50 transition-all cursor-pointer group-hover:border-teal-200"
                  >
                    {CATEGORIES.map((cat) => (
                      <option key={cat.id} value={cat.id} className="font-medium">
                        {cat.label}
                      </option>
                    ))}
                  </select>
                  <div className="pointer-events-none absolute inset-y-0 right-5 flex items-center text-slate-400">
                    <svg className="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                  </div>
                </div>
             </div>

             {/* SÜRÜM NOTU */}
             <div className="sticky top-10 p-6 bg-teal-50/50 dark:bg-teal-950/50 rounded-[2rem] border border-teal-100 dark:border-teal-800">
                <h3 className="font-serif text-xl italic mb-3 flex items-center gap-2 text-teal-900 dark:text-teal-100">
                  <History className="w-5 h-5" /> Sürüm Notu
                </h3>
                <textarea 
                  value={versionNote}
                  onChange={(e) => setVersionNote(e.target.value)}
                  className="w-full bg-white/70 dark:bg-slate-950/70 border border-teal-100 dark:border-teal-800 rounded-2xl p-4 text-sm focus:ring-2 ring-teal-200 outline-none min-h-[150px] placeholder:text-teal-300 dark:placeholder:text-teal-700 text-teal-900 dark:text-teal-100 resize-y"
                  placeholder="Bu sürümde neyi geliştirdin? (v1.2)"
                />
             </div>
          </aside>
        </div>
      </main>

      {previewOpen && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4 animate-in fade-in duration-200 overflow-y-auto">
          <div className="bg-white dark:bg-slate-950 w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-[2.5rem] shadow-2xl relative my-8">
            <div className="sticky top-0 bg-white/90 dark:bg-slate-950/90 backdrop-blur-md border-b border-slate-100 dark:border-slate-800 p-8 flex justify-between items-center z-10">
              <h2 className="font-serif text-3xl italic tracking-tighter text-slate-900 dark:text-white">Yazı Ön İzleme</h2>
              <Button variant="ghost" size="icon" onClick={() => setPreviewOpen(false)} className="rounded-full hover:bg-red-50 hover:text-red-600">
                <X className="w-5 h-5" />
              </Button>
            </div>
            
            <div className="p-12 pb-20 space-y-8">
              {coverImage && (
                <div className="w-full aspect-[21/9] rounded-3xl overflow-hidden shadow-sm border border-slate-100 dark:border-slate-800">
                  <img src={coverImage} alt="Kapak Ön İzleme" className="w-full h-full object-cover" />
                </div>
              )}
              <h1 className="font-serif text-5xl italic text-slate-900 dark:text-white">{title || "Başlıksız Hikaye"}</h1>
              <div className="prose prose-lg max-w-none font-sans dark:prose-invert ProseMirror ProseMirror-preview" dangerouslySetInnerHTML={{ __html: editorHtml }} />
            </div>
          </div>
        </div>
      )}
    </div>
  )
}