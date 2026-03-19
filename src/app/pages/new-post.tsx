import * as React from "react";
import { useEditor, EditorContent } from '@tiptap/react'
import StarterKit from '@tiptap/starter-kit'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
// 🚀 YENİ: Tiptap Resim Eklentisi (Bunu kullanabilmek için terminalde şu komutu çalıştır: npm install @tiptap/extension-image)
import Image from '@tiptap/extension-image' 
import { common, createLowlight } from 'lowlight'
import { 
  Bold, Italic, Heading1, Heading2, Quote, 
  Terminal, Save, Sparkles, History, Eye, X,
  ImagePlus, UploadCloud, ImageIcon, Trash2 // Yeni ikonlar
} from 'lucide-react'
import { Button } from "../components/ui/button";

const lowlight = createLowlight(common)

export function NewPostPage() {
  const [previewOpen, setPreviewOpen] = React.useState(false); 
  
  // 🚀 YENİ: Kapak Fotoğrafı State'i (Base64 formatında tutacağız)
  const [coverImage, setCoverImage] = React.useState<string | null>(null);
  
  // Yazı başlığı için state
  const [title, setTitle] = React.useState("");

  const editor = useEditor({
    extensions: [
      StarterKit,
      CodeBlockLowlight.configure({ lowlight }),
      // 🚀 YENİ: Resim eklentisini aktif ettik
      Image.configure({
        HTMLAttributes: {
          class: 'rounded-2xl border border-slate-100 shadow-sm max-w-full h-auto my-8', // Resimlere stil verdik
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
  })

  if (!editor) return null;

  const editorHtml = editor.getHTML();

  // 🚀 HİLE FONKSİYONU: Resmi Base64'e çevirir (Backend yokken resim göstermek için)
  const toBase64 = (file: File): Promise<string> => new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = () => resolve(reader.result as string);
    reader.onerror = reject;
  });

  // 🚀 1. Kapak Fotoğrafı Yükleme Dosya Seçici
  const coverInputRef = React.useRef<HTMLInputElement>(null);

  const handleCoverImageChange = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file) {
      // Backend yok, hile yapıp Base64'e çeviriyoruz
      const base64 = await toBase64(file);
      setCoverImage(base64); // Kapak fotoğrafını state'e kaydet
    }
  };

  // 🚀 2. Yazı İçi Resim Ekleme (Tiptap Toolbar)
  const inlineImageInputRef = React.useRef<HTMLInputElement>(null);

  const handleInlineImageUpload = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (file && editor) {
      // Backend yok, hile yapıp Base64'e çeviriyoruz
      const base64 = await toBase64(file);
      
      // Tiptap editörüne Base64 resmini ekle
      editor.chain().focus().setImage({ src: base64 }).run();
    }
    // Aynı resmi tekrar seçebilmek için inputu temizle
    if (event.target) event.target.value = '';
  };

  return (
    <div className="flex-1 flex flex-col bg-white font-sans dark:bg-background">
      
      {/* MEDIUM TASARIM STİLLERİ */}
      <style dangerouslySetInnerHTML={{__html: `
        .ProseMirror pre { background-color: #f8fafc !important; color: #334155 !important; padding: 1.5rem !important; border-radius: 1rem !important; border: 1px solid #f1f5f9 !important; margin: 1.5rem 0 !important; overflow-x: auto; }
        .ProseMirror code { background-color: #f8fafc !important; color: #0f766e !important; padding: 0.25rem 0.5rem !important; border-radius: 0.5rem !important; font-weight: 600 !important; }
        .ProseMirror pre code { background-color: transparent !important; padding: 0 !important; }
        .prose code::before, .prose code::after { content: none !important; }
        
        /* Tiptap Resim Seçimi Stili */
        .ProseMirror img.ProseMirror-selectednode {
          outline: 3px solid #0f766e; /* teal-700 */
          box-shadow: 0 0 0 6px rgba(15, 118, 110, 0.2);
        }
      `}} />

      {/* Gizli Dosya Inputları */}
      <input type="file" accept="image/*" ref={coverInputRef} onChange={handleCoverImageChange} className="hidden" />
      <input type="file" accept="image/*" ref={inlineImageInputRef} onChange={handleInlineImageUpload} className="hidden" />

      <main className="container mx-auto max-w-5xl py-12 px-6">
        
        {/* ÜST BİLGİ VE AKSİYONLAR */}
        <div className="flex justify-between items-end mb-10 pb-6 border-b border-slate-100">
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
            <Button className="bg-slate-900 text-white rounded-full px-8 hover:bg-teal-600 transition-all border-none">
              <Save className="w-4 h-4 mr-2" /> Yayınla
            </Button>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
          
          {/* ANA EDİTÖR ALANI */}
          <div className="lg:col-span-3 space-y-8">
            
            {/* 🚀 1. KAPAK FOTOĞRAFI YÜKLEME ALANI */}
            <div className="w-full aspect-[21/9] bg-slate-50 dark:bg-slate-900 rounded-[2rem] border-2 border-dashed border-slate-200 dark:border-slate-800 hover:border-teal-200 transition-all overflow-hidden group relative flex items-center justify-center">
              {coverImage ? (
                <>
                  {/* Kapak Resmi Ön İzleme */}
                  <img src={coverImage} alt="Kapak Fotoğrafı" className="w-full h-full object-cover" />
                  {/* Düzenleme/Silme Butonları (Hover durumunda çıkar) */}
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
                // Yükleme İstemcisi
                <button 
                  onClick={() => coverInputRef.current?.click()}
                  className="text-center p-12 flex flex-col items-center gap-4 text-slate-500 hover:text-teal-600"
                >
                  <UploadCloud className="w-12 h-12 text-slate-300 group-hover:text-teal-400 transition-colors" />
                  <div className="space-y-1">
                    <p className="font-semibold text-slate-700 dark:text-slate-300">Yazı Kapak Fotoğrafı Ekle</p>
                    <p className="text-xs text-slate-400">Önerilen boyut: 1600x900px (Maksimum 5MB simülasyon)</p>
                  </div>
                </button>
              )}
            </div>

            {/* BAŞLIK GİRİŞİ */}
            <input 
              type="text"
              placeholder="Hikayenin Başlığı..."
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full font-serif text-6xl italic border-none focus:ring-0 outline-none p-0 text-slate-900 dark:text-foreground placeholder:text-slate-200"
            />

            {/* EDİTÖR VE TOOLBAR */}
            <div className="border border-slate-100 dark:border-border rounded-[2.5rem] shadow-sm bg-white dark:bg-card overflow-hidden relative">
              
              {/* 🛠️ KALICI TOOLBAR */}
              <div className="flex flex-wrap items-center gap-1 p-3 border-b border-slate-50 dark:border-border bg-slate-50/50 dark:bg-muted/50 sticky top-0 z-10 backdrop-blur-sm">
                
                {/* 🚀 2. YAZI İÇİ RESİM EKLEME BUTONU */}
                <button 
                  type="button"
                  onClick={() => inlineImageInputRef.current?.click()}
                  className="flex items-center gap-2 px-3 py-1.5 rounded-xl transition-all font-bold text-xs text-slate-600 hover:bg-slate-900 hover:text-white"
                  title="Yazı İçine Resim Ekle"
                >
                  <ImagePlus className="w-4 h-4" /> RESİM EKLE
                </button>

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

          {/* SAĞ PANEL: VERSİYON NOTU */}
          <aside className="lg:col-span-1 space-y-4">
             <div className="sticky top-10 p-6 bg-teal-50/50 dark:bg-teal-950/50 rounded-[2rem] border border-teal-100 dark:border-teal-800">
                <h3 className="font-serif text-xl italic mb-3 flex items-center gap-2 text-teal-900 dark:text-teal-100">
                  <History className="w-5 h-5" /> Sürüm Notu
                </h3>
                <textarea 
                  className="w-full bg-white/70 dark:bg-card/70 border border-teal-100 dark:border-teal-800 rounded-2xl p-4 text-sm focus:ring-2 ring-teal-200 outline-none min-h-[150px] placeholder:text-teal-300 text-teal-900 dark:text-teal-100"
                  placeholder="Bu sürümde neyi geliştirdin? (v1.2)"
                />
             </div>
          </aside>
        </div>
      </main>

      {/* 👀 SAF TAILWIND ÖN İZLEME MODALI */}
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
              {/* Ön İzleme Kapak Fotoğrafı */}
              {coverImage && (
                <div className="w-full aspect-[21/9] rounded-3xl overflow-hidden shadow-sm border border-slate-100">
                  <img src={coverImage} alt="Kapak Ön İzleme" className="w-full h-full object-cover" />
                </div>
              )}

              {/* Ön İzleme Başlık */}
              <h1 className="font-serif text-5xl italic text-slate-900 dark:text-white">{title || "Başlıksız Hikaye"}</h1>

              <div 
                className="prose prose-lg max-w-none font-sans dark:prose-invert ProseMirror ProseMirror-preview"
                dangerouslySetInnerHTML={{ __html: editorHtml }} 
              />
            </div>
          </div>
        </div>
      )}

    </div>
  )
}