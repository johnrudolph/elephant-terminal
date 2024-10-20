import TileInput from '@/Components/Game/TileInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout'
import { Head, usePage } from '@inertiajs/react'
import { post } from '@/Utilities/http-client'

export default function Game({ game }) {
  const board = Object.entries(game.board);

  const props = usePage().props;

  const playTile = (space, direction) => {
    post(
      route('games.play_tile', {game: game.id}),
      {
          game_id: game.id,
          space: space,
          direction: direction,
      },
      props.csrf_token
    )
  }

  return (
    <AuthenticatedLayout
        header={
            <h2 className="text-xl font-semibold leading-tight text-gray-800">
                Game ({game.code})
            </h2>
        }
    >
        <Head title="Game" />
          <div className="grid grid-cols-6 w-full h-full justify-center mx-auto">
            <div className="h-full justify-center items-center"></div>
            <TileInput space="1" direction="down" onClick={() => playTile(1, "down")}></TileInput>
            <TileInput space="2" direction="down" onClick={() => playTile(2, "down")}></TileInput>
            <TileInput space="3" direction="down" onClick={() => playTile(3, "down")}></TileInput>
            <TileInput space="4" direction="down" onClick={() => playTile(4, "down")}></TileInput>
            <div className="h-full justify-center items-center"></div>
            <TileInput space="1" direction="right" onClick={() => playTile(1, "right")}></TileInput>
            {board.slice(0, 4).map((space, index) => (
              <div className="border border-black h-full justify-center items-center" key={index}>
                {game.elephant_space === parseInt(space) && <p>E</p>}
              </div>
            ))}
            <TileInput space="4" direction="left" onClick={() => playTile(4, "left")}></TileInput>
            <TileInput space="5" direction="right" onClick={() => playTile(5, "right")}></TileInput>
            {board.slice(4, 8).map((space, index) => (
              <div className="border border-black h-full justify-center items-center" key={index}>
                {game.elephant_space === parseInt(space) && <p>E</p>}
              </div>
            ))}
            <TileInput space="8" direction="left" onClick={() => playTile(8, "left")}></TileInput>
            <TileInput space="9" direction="right" onClick={() => playTile(9, "right")}></TileInput>
            {board.slice(8, 12).map((space, index) => (
              <div className="border border-black h-full justify-center items-center" key={index}>
                {game.elephant_space === parseInt(space) && <p>E</p>}
              </div>
            ))}
            <TileInput space="12" direction="left" onClick={() => playTile(12, "left")}></TileInput>
            <TileInput space="13" direction="right" onClick={() => playTile(1, "right")}></TileInput>
            {board.slice(12, 16).map((space, index) => (
              <div className="border border-black h-full justify-center items-center" key={index}>
                {game.elephant_space === parseInt(space) && <p>E</p>}
              </div>
            ))}
            <TileInput space="16" direction="left" onClick={() => playTile(16, "left")}></TileInput>
            <div className="h-full justify-center items-center"></div>
            <TileInput space="13" direction="up" onClick={() => playTile(13, "up")}></TileInput>
            <TileInput space="14" direction="up" onClick={() => playTile(14, "up")}></TileInput>
            <TileInput space="15" direction="up" onClick={() => playTile(15, "up")}></TileInput>
            <TileInput space="16" direction="up" onClick={() => playTile(16, "up")}></TileInput>
            <div className="h-full justify-center items-center"></div>
        </div>
    </AuthenticatedLayout>
  )
}